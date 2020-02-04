<?php

namespace Omnipay\Mpay24;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Mpay24\Messages\FetchTransactionRequest;
use Omnipay\Mpay24\Messages\Redirect\PurchaseRequest;
use Omnipay\Mpay24\Messages\Redirect\CompletePurchaseRequest;
use Omnipay\Mpay24\Messages\AcceptNotification;

class RedirectGateway extends AbstractGateway implements ConstantsInterface
{
    use ParameterTrait;

    /**
     * @return string
     */
    public function getName()
    {
        return 'mPAY24';
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'merchantId' => '',
            'password' => '',
        );
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
     * Handle result on return from redirect.
     * The documentation warns not to use this return URL to indicate the
     * status. Use the notify URL instead.
     *
     * @param  array $parameters
     * @return CompletePurchaseRequest
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
