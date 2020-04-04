<?php

namespace Omnipay\Mpay24\Messages\Backend;

use Omnipay\Mpay24\Messages\AbstractMpay24Request;
use Mpay24\Mpay24Order;

class DeleteProfileRequest extends AbstractMpay24Request
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
            'profileId' => $this->getProfileId(),
        ];
    }

    /**
     * @param array $data
     * @return ResponseInterface|ListProfilesResponse
     */
    public function sendData($data)
    {
        $mpay24 = $this->getMpay();

        $result = $mpay24->deleteProfile(
            $data['customerId'],
            $data['profileId']
        );

        $resultData = [
            'operationStatus' => $result->getStatus(),
            'returnCode' => $result->getReturnCode(),
        ];

        return new Response($this, $resultData);
    }
}
