<?php

namespace Omnipay\Mpay24;

//use Omnipay\Common\AbstractGateway;
//use Omnipay\Common\Message\RequestInterface;
use Omnipay\Mpay24\Messages\FetchTransactionRequest;
use Omnipay\Mpay24\Messages\Seamless\TokenRequest;
use Omnipay\Mpay24\Messages\Seamless\PurchaseRequest;
use Omnipay\Mpay24\Messages\CompletePurchaseRequest;
use Omnipay\Mpay24\Messages\AcceptNotification;

class SeamlessGateway extends PaymentPageGateway
{
    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        $parameters = parent::getDefaultParameters();

        $parameters['manualClearing'] = false;
        $parameters['useProfile'] = false;

        $parameters['paymentType'] = static::PTYPE_TOKEN;

        return $parameters;
    }

    /**
     * @param  array $parameters
     * @return TokenRequest
     */
    public function token(array $parameters = [])
    {
        return $this->createRequest(TokenRequest::class, $parameters);
    }

    /**
     * @param  array $parameters
     * @return PurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest(PurchaseRequest::class, $parameters);
    }

    /**
     * @param  array $parameters
     * @return PurchaseRequest
     */
    public function authorize(array $parameters = [])
    {
        return $this->createRequest(PurchaseRequest::class, $parameters);
    }

    /**
     * @param  array $parameters
     * @return PurchaseRequest
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest(CompletePurchaseRequest::class, $parameters);
    }
}
