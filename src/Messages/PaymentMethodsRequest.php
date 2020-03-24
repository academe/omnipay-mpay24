<?php

namespace Omnipay\Mpay24\Messages;

/**
 * Capture an authorized transaction.
 * Support for authorize vs purchase, is set account-wide in the mPAY24 portal.
 */

use Omnipay\Mpay24\Messages\AbstractMpay24Request;

class PaymentMethodsRequest extends AbstractMpay24Request
{
    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        return [];
    }

    public function sendData($data)
    {
        $mpay24 = $this->getMpay();

        $result = $mpay24->paymentMethods();

        $resultData = [
            'paymentTypes' => $result->getPTypes(),
            'brands' => $result->getBrands(),
            'descriptions' => $result->getDescriptions(),
            'paymentMethodIds' => $result->getPMethIDs(),
        ];

        return new PaymentMethodsResponse($this, $resultData);
    }
}
