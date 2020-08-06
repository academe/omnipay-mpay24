<?php

namespace Omnipay\Mpay24\Messages;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Mpay24\ParameterTrait;
use Omnipay\Mpay24\ConstantsInterface;
use Mpay24\Mpay24;
use Mpay24\Mpay24Config;
use Money\Money;
use Money\Number;
//use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;

abstract class AbstractMpay24Request extends AbstractRequest implements ConstantsInterface
{
    use ParameterTrait;

    /**
     * Return the Mpay24 SDK object, with all settings configured.
     */
    protected function getMpay()
    {
        $mpay24config = new Mpay24Config();

        $mpay24config->useTestSystem($this->getTestMode());
        $mpay24config->setDebug((bool)$this->getDebug());
        $mpay24config->setMerchantId($this->getMerchantId());
        $mpay24config->setSoapPassword($this->getPassword());

        $mpay24 = new Mpay24($mpay24config);

        return $mpay24;
    }

    /**
     * @return string|null
     */
    protected function getCustomerName()
    {
        $card = $this->getCard();

        if ($card) {
            return $card->getName();
        }
    }

    /**
     * Collect the billing address data.
     * The billing name is the only mandatory field, so this determines
     * if an address can be returned.
     *
     * @return array
     */
    protected function getBillingAddressData()
    {
        $card = $this->getCard();

        if (! $card) {
            // No card, so no address details at all.
            return [];
        }

        if (! $card->getBillingName()) {
            // The name is mandatory - cannot have an address without one.
            return [];
        }

        $addressMode = $this->getAddressMode() === static::ADDRESS_MODE_READWRITE
            ? static::ADDRESS_MODE_READWRITE
            : static::ADDRESS_MODE_READONLY;

        $countryCode = preg_match('/^[A-Z]{2}$/i', $card->getBillingCountry())
            ? strtoupper($card->getBillingCountry())
            : null;

        $rawGender = strtoupper($card->getGender());

        $gender = ($rawGender === static::GENDER_MALE || $rawGender === static::GENDER_FEMALE)
            ? $rawGender
            : null;

        return [
            'mode' => $addressMode,
            'name' => $card->getBillingName(),
            'gender' => $gender,
            'birthday' => $card->getBirthday(),
            'street' => $card->getBillingAddress1(),
            'street2' => $card->getBillingAddress2(),
            'zip' => $card->getBillingPostcode(),
            'city' => $card->getBillingCity(),
            'state' => $card->getBillingState(),
            'countryCode' => $countryCode,
            'email' => $card->getEmail(),
            'phone' => $card->getBillingPhone(),
        ];
    }

    /**
     * Collect the shipping address data.
     * The billing name is the only mandatory field, so this determines
     * if an address can be returned.
     *
     * @return array
     */
    protected function getShippingAddressData()
    {
        $card = $this->getCard();

        if (! $card) {
            // No card, so no address details at all.
            return [];
        }

        if (! $card->getShippingName()) {
            // The name is mandatory - cannot have an address without one.
            return [];
        }

        $addressMode = $this->getAddressMode() === static::ADDRESS_MODE_READWRITE
            ? static::ADDRESS_MODE_READWRITE
            : static::ADDRESS_MODE_READONLY;

        $countryCode = preg_match('/^[A-Z]{2}$/i', $card->getShippingCountry())
            ? strtoupper($card->getShippingCountry())
            : null;

        return [
            'mode' => $addressMode,
            'name' => $card->getShippingName(),
            'street' => $card->getShippingAddress1(),
            'street2' => $card->getShippingAddress2(),
            'zip' => $card->getShippingPostcode(),
            'city' => $card->getShippingCity(),
            'state' => $card->getShippingState(),
            'countryCode' => $countryCode,
            'phone' => $card->getShippingPhone(),
        ];
    }

    /**
     * Return the items basket/cart as data with mPAY24 key names.
     */
    public function getShoppingCartData(): array
    {
        $data = [];

        if (! empty($this->getItems())) {
            $itemNumber = 0;

            foreach ($this->getItems() as $item) {
                $itemNumber++;

                $currencyCode = $this->getCurrency();
                $currency = new Currency($currencyCode);

                $moneyParser = new DecimalMoneyParser($this->getCurrencies());

                $number = Number::fromString($item->getPrice());

                $money = $moneyParser->parse((string) $number, $currency);

                $data[$itemNumber] = [
                    'number' => $itemNumber,
                    'productNr' => $item->getName(),
                    'description' => $item->getDescription() ?: $item->getName(),
                    'quantity' => $item->getQuantity(),
                    'itemPrice' => $item->getPrice(), // Major units
                    'amount' => $money->getAmount(), // Minor units
                ];
            }
        }

        return $data;
    }
}
