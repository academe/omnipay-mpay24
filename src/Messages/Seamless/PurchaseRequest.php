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
        return [];
    }

    /**
     * @param array $data
     * @return ResponseInterface|PurchaseResponse
     */
    public function sendData($data)
    {
        $mpay24 = $this->getMpay();

        $tokenizer = $mpay24->token('CC');

        $tokenizerLocation = $tokenizer->getLocation();
        $token = $tokenizer->getToken();

        // TODO: cater for errors in fetching the token and location.

        return new TokenResponse($this, [
            'tokenizerLocation' => $tokenizerLocation,
            'token' => $token,
        ]);
    }
}
