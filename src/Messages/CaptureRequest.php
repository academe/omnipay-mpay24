<?php

namespace Omnipay\Mpay24\Messages;

/**
 * Capture an authorized transaction.
 * Support for authorize vs purchase, is set account-wide in the mPAY24 portal.
 */

use Omnipay\Mpay24\Messages\AbstractMpay24Request;
use Mpay24\Mpay24Order;

class CaptureRequest extends AbstractMpay24Request
{
    /**
     * @return array
     * @throws InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        $this->validate('transactionReference');

        return [
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

        $result = $mpay24->capture($data['transactionReference']);

        $params = $result->getParams();

        $params['operationStatus'] = $result->getStatus();
        $params['returnCode'] = $result->getReturnCode();

        return new CaptureResponse($this, $params);
    }
}
