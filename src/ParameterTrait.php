<?php

namespace Omnipay\Mpay24;

trait ParameterTrait
{
    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->getParameter('password');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    /**
     * @return string
     */
    public function getErrorUrl()
    {
        return $this->getParameter('errorUrl');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setErrorUrl($value)
    {
        return $this->setParameter('errorUrl', $value);
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->getParameter('language');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setLanguage($value)
    {
        return $this->setParameter('language', $value);
    }

    /**
     * @return string
     */
    public function getManualClearing()
    {
        return $this->getParameter('manualClearing');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setManualClearing($value)
    {
        return $this->setParameter('manualClearing', $value);
    }

    /**
     * @return string
     */
    public function getUseProfile()
    {
        return $this->getParameter('useProfile');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setUseProfile($value)
    {
        return $this->setParameter('useProfile', $value);
    }

    /**
     * @return string
     */
    public function getUserField()
    {
        return $this->getParameter('userField');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setUserField($value)
    {
        return $this->setParameter('userField', $value);
    }
}
