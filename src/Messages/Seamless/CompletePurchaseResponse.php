<?php

namespace Omnipay\Mpay24\Messages\Seamless;

use Omnipay\Mpay24\Messages\AbstractMpay24Response;
use Omnipay\Common\Message\RedirectResponseInterface;

class CompletePurchaseResponse extends AbstractMpay24Response implements RedirectResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        // TODO.

        return $this->getCode()
            && true;
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
    public function redirectMethod()
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
    public function redirectUrl()
    {
        return ! empty($this->data['redirectUrl']) ? $this->data['redirectUrl'] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return ! empty($this->data['returnCode']) ? $this->data['returnCode'] : null;
    }

    /**
     * The operation see OPERATION_STATUS_* constants.
     */
    public function getOperationStatus()
    {
        return ! empty($this->data['status']) ? $this->data['status'] : null;
    }

    /**
     * The transaction reference may still be available even if there
     * is a further redirect needed for Secure 3D or equivalent.
     */
    public function getTransactionReference()
    {
        return ! empty($this->data['transactionReference']) ? $this->data['transactionReference'] : null;
    }
}
