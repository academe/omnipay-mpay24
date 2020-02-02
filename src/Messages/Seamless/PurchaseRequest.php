<?php

namespace Omnipay\Mpay24\Messages\Seamless;

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

        $type = 'TOKEN';

        $additional = [
            'successURL' => $this->getReturnUrl(),
            'errorURL' => $this->getErrorUrl() ?: $this->getReturnUrl(),
            'confirmationURL' => $this->getNotifyUrl(),
        ];

        $payment = [
            'amount' => $this->getAmountInteger(),
            'currency' => $this->getCurrency(),
            // Optional: set to true if you want to do a manual clearing
            'manualClearing' => $this->getManualClearing() ? 'true' : 'false',
            // Optional: set if you want to create a profile
            'useProfile' => $this->getUseProfile() ? 'true' : 'false',
        ];

        $card = $this->getCard();

        // TODO: check out other types.
        if ($type === 'TOKEN') {
            $payment['token'] = $this->getToken();
        }

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

        // TODO: populate shopping cart.

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
            'type' => $type,
            'tid' => $this->getTransactionId(),
            'payment' => $payment,
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
            $data['type'],
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
}
