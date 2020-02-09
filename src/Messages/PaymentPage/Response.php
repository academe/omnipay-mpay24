<?php

namespace Omnipay\Mpay24\Messages\PaymentPage;

use Omnipay\Mpay24\Messages\AbstractMpay24Response;
use Omnipay\Common\Message\RedirectResponseInterface;

class Response extends AbstractMpay24Response implements RedirectResponseInterface
{
    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isRedirect()
    {
        return ! empty($this->data['redirectUrl']);
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectUrl()
    {
        if ($this->isRedirect()) {
            return $this->data['redirectUrl'];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectData()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectMethod()
    {
        return 'GET';
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return isset($this->data['returnCode']) ? $this->data['returnCode'] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        // 'OK' or 'ERROR'
        return isset($this->data['status']) ? $this->data['status'] : null;
    }
}
