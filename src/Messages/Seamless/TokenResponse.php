<?php

namespace Omnipay\Mpay24\Messages\Seamless;

use Omnipay\Mpay24\Messages\AbstractMpay24Response;
use Omnipay\Mpay24\Messages\NotificationValuesTrait;
use Omnipay\Common\Message\RedirectResponseInterface;

class TokenResponse extends AbstractMpay24Response implements RedirectResponseInterface
{
    use NotificationValuesTrait;

    /**
     * True if a token was successfully fetched.
     */
    public function isSuccessful()
    {
        return $this->getOperationStatus() === static::OPERATION_STATUS_OK;
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectMethod()
    {
        return 'TOKEN';
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectUrl()
    {
        return $this->getTokenizerLocation();
    }

    public function getTokenizerLocation()
    {
        return $this->getDataItem('tokenizerLocation');
    }

    public function getToken()
    {
        return $this->getDataItem('token');
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectData()
    {
        return [
            'tokenizerLocation' => $this->getTokenizerLocation(),
            'token' => $this->getToken(),
        ];
    }

    /**
     * Is the response a transparent redirect?
     *
     * @return boolean
     */
    public function isTransparentRedirect()
    {
        return true;
    }
}
