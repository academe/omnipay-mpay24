<?php

namespace Omnipay\Mpay24;

use Omnipay\Mpay24\Messages\Backend\ListProfilesRequest;
use Omnipay\Mpay24\Messages\Backend\DeleteProfileRequest;
use Omnipay\Mpay24\Messages\FetchTransactionRequest;
use Omnipay\Mpay24\Messages\Backend\PurchaseRequest;
use Omnipay\Mpay24\Messages\AcceptNotification;

class BackendGateway extends PaymentPageGateway
{
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
     * @param  array $parameters
     * @return ListProfilesRequest
     */
    public function listProfiles(array $parameters = [])
    {
        return $this->createRequest(ListProfilesRequest::class, $parameters);
    }

    /**
     * @param  array $parameters
     * @return DeleteProfileRequest
     */
    public function deleteProfile(array $parameters = [])
    {
        return $this->createRequest(DeleteProfileRequest::class, $parameters);
    }

    /**
     * @param  array $parameters
     * @return DeleteProfileRequest
     */
    public function deleteCard(array $parameters = [])
    {
        return $this->deletePofile($parameters);
    }
}
