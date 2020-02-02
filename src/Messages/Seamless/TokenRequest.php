<?php

namespace Omnipay\Mpay24\Messages\Seamless;

use Omnipay\Mpay24\Messages\AbstractMpay24Request;
use Mpay24\Mpay24Order;

class TokenRequest extends AbstractMpay24Request
{
    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        $additional = [];

        if ($this->getLanguage()) {
            $additional['language'] = strtoupper($this->getLanguage());
        }

        if ($this->getCustomerId()) {
            $additional['customerID'] = $this->getCustomerId();
        }

        if ($this->getProfileId()) {
            $additional['profileID'] = $this->getProfileId();
        }

        if ($this->getTemplateSet()) {
            $additional['templateSet'] = $this->getTemplateSet();
        }

        if ($this->getStyle()) {
            $additional['style'] = $this->getStyle();
        }

        if ($this->getDomain()) {
            $additional['domain'] = $this->getDomain();
        }

        return [
            'additional' => $additional,
        ];
    }

    /**
     * @param array $data
     * @return ResponseInterface|PurchaseResponse
     */
    public function sendData($data)
    {
        $mpay24 = $this->getMpay();

        $tokenizer = $mpay24->token('CC', $data['additional']);

        $tokenizerLocation = $tokenizer->getLocation();
        $token = $tokenizer->getToken();

        // TODO: cater for errors in fetching the token and location.

        return new TokenResponse($this, [
            'tokenizerLocation' => $tokenizerLocation,
            'token' => $token,
            'operationStatus' => $tokenizer->getStatus(),
            'returnCode' => $tokenizer->getReturnCode(),
        ]);
    }
}
