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
            // Required if useProfile is true
            'customerID' => "customer123",
            'customerName' => "Jon Doe",
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

        if (! empty($order)) {
            $additional['order'] = $order;
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
            'status' => $result->getStatus(),
            'errNo' => $result->getErrNo(),
            'errText' => $result->getErrText(),
            'transactionReference' => $result->getMpayTid(),
            'returnCode' => $result->getReturnCode(),
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
