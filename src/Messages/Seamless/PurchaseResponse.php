<?php

namespace Omnipay\Mpay24\Messages\Seamless;

use Omnipay\Mpay24\Messages\AbstractMpay24Response;
use Omnipay\Common\Message\RedirectResponseInterface;

class PurchaseResponse extends AbstractMpay24Response implements RedirectResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        // TODO: additional checks needed for non-redirect results.

        return $this->operationSuccessful()
            && ! $this->isRedirect();
    }

    /**
     * {@inheritdoc}
     */
    public function isRedirect()
    {
        return $this->getCode() === static::RETURN_CODE_REDIRECT;
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectMethod()
    {
        return 'get';
    }

    /**
     * {@inheritdoc}
     */
    public function isCancelled()
    {
        //return $this->getCode() === ???;
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectUrl()
    {
        return $this->getDataItem('redirectUrl');
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getDataItem('returnCode');
    }

    /**
     * The operation see OPERATION_STATUS_* constants.
     */
    public function getOperationStatus()
    {
        return $this->getDataItem('status');
    }

    public function operationSuccessful()
    {
        return $this->getOperationStatus() === static::OPERATION_STATUS_OK;
    }

    /**
     * The transaction reference may still be available even if there
     * is a further redirect needed for Secure 3D or equivalent.
     */
    public function getTransactionReference()
    {
        return $this->getDataItem('transactionReference');
    }
}
