<?php

namespace Omnipay\Mpay24;

//use Omnipay\Common\AbstractGateway;
//use Omnipay\Common\Message\RequestInterface;
use Omnipay\Mpay24\Messages\FetchTransactionRequest;
use Omnipay\Mpay24\Messages\Seamless\TokenRequest;
use Omnipay\Mpay24\Messages\Seamless\PurchaseRequest;
use Omnipay\Mpay24\Messages\Seamless\CompletePurchaseRequest;
use Omnipay\Mpay24\Messages\Seamless\CompletePurchaseResponse;
use Omnipay\Mpay24\Messages\AcceptNotification;

class SeamlessGateway extends RedirectGateway
{
    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        $parameters = parent::getDefaultParameters();

        $parameters['manualClearing'] = false;
        $parameters['useProfile'] = false;

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
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest(CompletePurchaseRequest::class, $parameters);
    }

    public function acceptNotification(array $parameters = [])
    {
        return $this->createRequest(AcceptNotification::class, $parameters);
    }

    public function fetchTransaction(array $parameters = [])
    {
        return $this->createRequest(FetchTransactionRequest::class, $parameters);
    }
}
