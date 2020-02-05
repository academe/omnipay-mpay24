<?php

namespace Omnipay\Mpay24\Messages\Seamless;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Mpay24\Messages\AbstractMpay24Request;
use Mpay24\Mpay24Order;

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

        $additional = [
            'successURL' => $this->getReturnUrl(),
            'errorURL' => $this->getErrorUrl() ?: $this->getReturnUrl(),
            'confirmationURL' => $this->getNotifyUrl(),
        ];

        $card = $this->getCard();

        if (! empty($this->getLanguage())) {
            $additional['language'] = strtoupper($this->getLanguage());
        }

        $order = [];

        if ($this->getDescription()) {
            $order['description'] = $this->getDescription();
        }

        if ($this->getClientIp()) {
            $order['clientIP'] = $this->getClientIp();
        }

        if ($this->getUserField()) {
            $order['userField'] = $this->getUserField();
        }

        $billingAddress = [];
        $shippingAddress = [];

        if ($card) {
            if ($card->getName()) {
                $additional['customerName'] = $card->getName();
            }

            if ($card->getBillingName() && $card->getBillingCountry()) {
                // Populate billing address.

                // All mandatory fields, according to the docs.
                // However, they do appear to be optional.

                $billingAddress['name'] = $card->getBillingName();
                $billingAddress['street'] = $card->getBillingAddress1();
                $billingAddress['street2'] = $card->getBillingAddress2();
                $billingAddress['zip'] = $card->getBillingPostcode();
                $billingAddress['city'] = $card->getBillingCity();
                $billingAddress['countryCode'] = $card->getBillingCountry();
            }

            if ($card->getShippingName() && $card->getShippingCountry()) {
                // Populate shipping address.

                $shippingAddress['name'] = $card->getShippingName();
                $shippingAddress['street'] = $card->getShippingAddress1();
                $shippingAddress['street2'] = $card->getShippingAddress2();
                $shippingAddress['zip'] = $card->getShippingPostcode();
                $shippingAddress['city'] = $card->getShippingCity();
                $shippingAddress['countryCode'] = $card->getShippingCountry();
            }
        }

        if (! empty($billingAddress)) {
            $order['billing'] = $billingAddress;
        }

        if (! empty($shippingAddress)) {
            $order['shipping'] = $shippingAddress;
        }

        $shoppingCart = [];

        // Populate shopping cart.
        // For details of what is supported, see:
        // https://docs.mpay24.com/docs/soap-interface#section-shopping-cart

        $items = $this->getItems();

        if (! empty($items) && count($items) > 0) {
            foreach ($items->all() as $item) {
                // TODO
            }
        }

        if (! empty($shoppingCart)) {
            $order['shoppingCart'] = $shoppingCart;
        }

        if (! empty($order)) {
            $additional['order'] = $order;
        }

        // Required if useProfile is true
        if ($this->getCustomerId()) {
            $additional['customerID'] = $this->getCustomerId();
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
     * The incoming server request.
     */
    protected function getServerRequest()
    {
        return $this->httpRequest;
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

        if ($ptype === static::PTYPE_CC) {
            $card = $this->getCard();

            $this->validate('card');
            $card->validate();

            $payment['brand'] = 'VISA'; // FIXME: map from OmniPay brand to mPAY24 brand
            $payment['identifier'] = $card->getNumber();
            $payment['expiry'] = $card->getExpiryDate('ym');
            $payment['cvv'] = $card->getCvv();
            $payment['auth3DS'] = 'true'; // FIXME: make this an option

            return $payment;
        }

        if ($ptype === static::PTYPE_PAYPAL) {
            $payment['commit'] = (bool)$this->getCommit();
            $payment['custom'] = $this->getUserField();

            return $payment;
        }

        if ($ptype === static::PTYPE_KLARNA) {
            $payment['brand'] = 'INVOICE';
            //$payment['brand'] = 'HP';
            $payment['personalNumber'] = 'tbc';

            $payment['pClass'] = '';

            return $payment;
        }

        if ($ptype === static::PTYPE_ELV) {
            // See https://docs.mpay24.com/docs/direct-debit
            // Required: IP address, billing address (with name), follow SEPA standing orders

            $payment['brand'] = 'BILLPAY'; // FIXME

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
