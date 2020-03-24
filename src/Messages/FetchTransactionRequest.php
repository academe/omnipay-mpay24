<?php

namespace Omnipay\Mpay24\Messages;

/**
 * Fetch a transaction by 
 */

use Omnipay\Mpay24\Messages\AbstractMpay24Request;

class FetchTransactionRequest extends AbstractMpay24Request
{
    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        // One or the other is required.
        // TODO: do some validation.

        return [
            'transactionId' => $this->getTransactionId(),
            'transactionReference' => $this->getTransactionReference(),
        ];
    }

    /**
     * @return array
     * @throws PurchaseReponse
     */
    public function sendData($data)
    {
        $mpay24 = $this->getMpay();

        if (! empty($data['transactionId'])) {
            $result = $mpay24->paymentStatusByTID($data['transactionId']);
        }

        if (! empty($data['transactionReference'])) {
            $result = $mpay24->paymentStatus($data['transactionReference']);
        }

        $params = $result->getParams();

        $params['operationStatus'] = $result->getStatus();
        $params['returnCode'] = $result->getReturnCode();

        return new FetchTransactionResponse($this, $params);
    }
}
