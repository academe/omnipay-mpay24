<?php

namespace Omnipay\Mpay24;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Mpay24\Messages\FetchTransactionRequest;
use Omnipay\Mpay24\Messages\PaymentPage\PurchaseRequest;
use Omnipay\Mpay24\Messages\CompletePurchaseRequest;
use Omnipay\Mpay24\Messages\AcceptNotification;
use Omnipay\Mpay24\Messages\CaptureRequest;
use Omnipay\Mpay24\Messages\PaymentMethodsRequest;

class PaymentPageGateway extends AbstractGateway implements ConstantsInterface
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
        return [
            'merchantId' => '',
            'password' => '',
            'cssName' => ConstantsInterface::CSS_NAME_MODERN,
        ];
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
        return $this->purchase($parameters);
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

    public function capture(array $parameters = [])
    {
        return $this->createRequest(CaptureRequest::class, $parameters);
    }

    public function paymentMethods(array $parameters = [])
    {
        return $this->createRequest(PaymentMethodsRequest::class, $parameters);
    }
}
