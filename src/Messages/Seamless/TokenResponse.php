<?php

namespace Omnipay\Mpay24\Messages\Seamless;

use Omnipay\Mpay24\Messages\AbstractMpay24Response;
use Omnipay\Common\Message\RedirectResponseInterface;

class TokenResponse extends AbstractMpay24Response implements RedirectResponseInterface
{
    public function isSuccessful()
    {
        // Not yet complete.

        return false;
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
        return isset($this->data['tokenizerLocation']) ? $this->data['tokenizerLocation'] : null;
    }

    public function getToken()
    {
        return isset($this->data['token']) ? $this->data['token'] : null;
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
