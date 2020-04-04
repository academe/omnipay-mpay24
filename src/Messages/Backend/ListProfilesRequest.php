<?php

namespace Omnipay\Mpay24\Messages\Backend;

use Omnipay\Mpay24\Messages\AbstractMpay24Request;
use Mpay24\Mpay24Order;

class ListProfilesRequest extends AbstractMpay24Request
{
    /**
     * The data key names are from the mPAY24 spec, but lower camelCase.
     *
     * @return array
     * @throws InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        return [
            'customerId' => $this->getCustomerId() ?? $this->getCardReference(),
            'expiredBy' => $this->getExpiredBy(),
            'begin' => $this->getBegin(),
            'size' => $this->getSize(),
        ];
    }

    /**
     * @param array $data
     * @return ResponseInterface|ListProfilesResponse
     */
    public function sendData($data)
    {
        $mpay24 = $this->getMpay();

        $result = $mpay24->listCustomers(
            $data['customerId'],
            $data['expiredBy'],
            $data['begin'],
            $data['size']
        );

        $resultData = [
            'operationStatus' => $result->getStatus(),
            'returnCode' => $result->getReturnCode(),
            'profiles' => $result->getProfiles(),
        ];

        return new ListProfilesResponse($this, $resultData);
    }
}