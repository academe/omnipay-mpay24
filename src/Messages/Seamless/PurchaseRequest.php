<?php

namespace Omnipay\Mpay24\Messages\Seamless;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Mpay24\Messages\AbstractMpay24Request;
use DateTimeInterface;
use DateTime;

class PurchaseRequest extends AbstractMpay24Request
{
    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        // Collect together all the details of the payment to be made
        // with the token.

        $additional = [];

        // Required if useProfile is true
        if ($this->getCustomerId()) {
            $additional['customerID'] = $this->getCustomerId();
        }

        if ($this->getCustomerName()) {
            $additional['customerName'] = $this->getCustomerName();
        }

        $order = [];

        if ($this->getClientIp()) {
            $order['clientIP'] = $this->getClientIp();
        }

        if ($this->getDescription()) {
            $order['description'] = $this->getDescription();
        }

        if ($this->getUserField()) {
            $order['userField'] = $this->getUserField();
        }

        if (! empty($this->getItems())) {
            $itemNumber = 0;

            $order['shoppingCart'] = [];

            foreach ($this->getItems() as $item) {
                $itemNumber++;

                $order['shoppingCart']['item-' . $itemNumber] = [
                    'number' => $itemNumber,
                    'productNr' => $item->getName(),
                    'description' => $item->getDescription() ?: $item->getName(),
                    //'package' => "Package 1",
                    'quantity' => $item->getQuantity(),
                    'itemPrice' => $item->getPrice(),
                    //'itemPrice'->setTax(1.23),
                    //'price' = 10.00,
                ];
            }
        }

        $billingAddress = array_filter($this->getBillingAddressData(), function ($value) {
            return !empty($value);
        });

        // PaymentPage needs this to be mixed case, and seamless needs it
        // to be upper case.

        if (isset($billingAddress['mode'])) {
            $billingAddress['mode'] = strtoupper($billingAddress['mode']);
        }

        $shippingAddress = array_filter($this->getShippingAddressData(), function ($value) {
            return !empty($value);
        });

        if (! empty($billingAddress)) {
            $order['billing'] = $billingAddress;
        }

        // PaymentPage needs this to be mixed case, and seamless needs it
        // to be upper case.

        if (isset($shippingAddress['mode'])) {
            $shippingAddress['mode'] = strtoupper($shippingAddress['mode']);
        }

        if (! empty($shippingAddress)) {
            $order['shipping'] = $shippingAddress;
        }

        if (! empty($order)) {
            $additional['order'] = $order;
        }

        $additional['successURL'] = $this->getReturnUrl();
        $additional['errorURL'] = $this->getErrorUrl() ?: $this->getReturnUrl();
        $additional['confirmationURL'] = $this->getNotifyUrl();

        if (! empty($this->getLanguage())) {
            $additional['language'] = strtoupper($this->getLanguage());
        }

        return [
            'ptype' => $this->getPaymentType(),
            'tid' => $this->getTransactionId(),
            'payment' => $this->getPaymentData(),
            'additional' => $additional,
        ];
    }

    /**
     * TODO: cater for responses that do not involve a redirect.
     *
     * @param array $data
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        $mpay24 = $this->getMpay();

        $result = $mpay24->payment(
            $data['ptype'],
            $data['tid'],
            $data['payment'],
            $data['additional']
        );

        $resultData = [
            'operationStatus' => $result->getStatus(),
            'returnCode' => $result->getReturnCode(),
            'errNo' => $result->getErrNo(),
            'errText' => $result->getErrText(),
            'transactionReference' => $result->getMpayTid(),
            'redirectUrl' => $result->getLocation(),
        ];

        return new PurchaseResponse($this, $resultData);
    }

    /**
     * Contstruct the payment data.
     */
    protected function getPaymentData()
    {
        $ptype = $this->getPaymentType();
        $brand = $this->getBrand();

        $payment = [
            'amount' => $this->getAmountInteger(),
            'currency' => $this->getCurrency(),
            // Optional: set to true if you want to do a manual clearing
            'manualClearing' => $this->getManualClearing() ? 'true' : 'false',
            // Optional: set if you want to create a profile
            'useProfile' => $this->getUseProfile() ? 'true' : 'false',
        ];

        if ($ptype === static::PTYPE_TOKEN) {
            $this->validate('token');

            $payment['token'] = $this->getToken();

            return $payment;
        }

        /*if ($ptype === static::PTYPE_CC) {
            $card = $this->getCard();

            $this->validate('card');
            $card->validate();

            $payment['brand'] = 'VISA'; // FIXME: map from OmniPay brand to mPAY24 brand
            $payment['identifier'] = $card->getNumber();
            $payment['expiry'] = $card->getExpiryDate('ym');
            $payment['cvv'] = $card->getCvv();
            $payment['auth3DS'] = 'true'; // FIXME: make this an option

            return $payment;
        }*/

        if ($ptype === static::PTYPE_PAYPAL) {
            $payment['commit'] = (bool)$this->getCommit() ? 'true' : 'false';
            $payment['custom'] = $this->getUserField();

            return $payment;
        }

        if ($ptype === static::PTYPE_SOFORT) {
            return $payment;
        }

        if ($ptype === static::PTYPE_KLARNA) {
            $this->validate('brand');
            $this->validate('personalNumber');

            $payment['brand'] = $this->getBrand();

            $payment['personalNumber'] = $this->getPersonalNumber();

            $payment['pClass'] = $this->getPClass();

            return $payment;
        }

        if ($ptype === static::PTYPE_ELV) {
            // See https://docs.mpay24.com/docs/direct-debit
            // Required: IP address, billing address (with name), follow SEPA standing orders.
            // TODO: validate that billing address is supplied for Heidelpay.

            $this->validate('brand');

            $brand = $this->getBrand();

            $payment['brand'] = $brand;
            $payment['iban'] = $this->getIban();
            $payment['bic'] = $this->getBic();

            if ($brand === static::BRAND_HOBEX_AT
                || $brand === static::BRAND_HOBEX_DE
                || $brand === static::BRAND_HOBEX_NL
                || $brand === static::BRAND_B4P
                || $brand === static::BRAND_HEIDELPAY
            ) {
                $this->validate('mandateId');
                $this->validate('dateOfSignature');
            }

            if (is_string($this->getDateOfSignature())) {
                $dateOfSignature = new DateTime($this->getDateOfSignature());
            }

            if ($this->getDateOfSignature() instanceof DateTimeInterface) {
                $dateOfSignature = $this->getDateOfSignature();
            }

            if (! empty($dateOfSignature)) {
                $payment['dateOfSignature'] = $dateOfSignature->format('Y-m-d');
            }

            if ($this->getMandateId()) {
                $payment['mandateID'] = $this->getMandateId();
            }

            return $payment;
        }

        if ($ptype === static::PTYPE_EPS) {
            if (! $this->getBankId() && ! $this->getBic()) {
                throw new InvalidRequestException('Either the bankId or bic parameter is required');
            }

            if ($this->getBankId()) {
                // Austrian bank.

                $payment['brand'] = $this->getBrand() ?: static::BRAND_EPS;
                $payment['bankID'] = $this->getBankId();
            } elseif ($this->getBic()) {
                // International bank.
                // In testing, the BIC only worked with the EPS brand, which contradicts the
                // documentation, so an override is provided here.

                $payment['brand'] = $this->getBrand() ?: static::BRAND_INTERNATIONAL;
                $payment['bic'] = $this->getBic();
            }

            return $payment;
        }

        // Exception: unknown or unsupported payment type.

        throw new InvalidRequestException(
            sprintf('Unknown paymentType "%s"', $ptype)
        );
    }
}
