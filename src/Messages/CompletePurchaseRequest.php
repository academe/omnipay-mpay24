<?php

namespace Omnipay\Mpay24\Messages;

use Mpay24\Mpay24Order;

class CompletePurchaseRequest extends AbstractMpay24Request
{
    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        // The result will be passed by query parameters, and that may be useful
        // for displaying a message on the front-end. However, that cannot be
        // trusted.
        // A notification will be sent to the merchant site (a push result),
        // but we can at this point perform a pull request to get the result of
        // the transaction.

        if (empty($this->getTransactionId()) && empty($this->getTransactionReference())) {
            $this->validate('transactionId');
            $this->validate('transactionReference');
        }

        return [
            'transactionId' => $this->getTransactionId(),
            'transactionReference' => $this->getTransactionReference(),
        ];
    }

    /**
     * @return array
     * @throws FetchTransactionResponse
     */
    public function sendData($data)
    {
        $mpay24 = $this->getMpay();

        if (! empty($data['transactionId'])) {
            $result = $mpay24->paymentStatusByTID($data['transactionId']);
        } elseif (! empty($data['transactionReference'])) {
            $result = $mpay24->paymentStatus($data['transactionReference']);
        }

        $params = $result->getParams();

        $params['operationStatus'] = $result->getStatus();
        $params['returnCode'] = $result->getReturnCode();

        return new FetchTransactionResponse($this, $params);
    }
}
