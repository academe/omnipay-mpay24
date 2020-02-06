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
    public function getProfileId()
    {
        return $this->getParameter('profileId');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setProfileId($value)
    {
        return $this->setParameter('profileId', $value);
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

    /**
     * @return string
     */
    public function getCustomerId()
    {
        return $this->getParameter('customerId');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setCustomerId($value)
    {
        return $this->setParameter('customerId', $value);
    }

    /**
     * @return string
     */
    public function getTemplateSet()
    {
        return $this->getParameter('templateSet');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setTemplateSet($value)
    {
        return $this->setParameter('templateSet', $value);
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        return $this->getParameter('style');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setStyle($value)
    {
        return $this->setParameter('style', $value);
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->getParameter('domain');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setDomain($value)
    {
        return $this->setParameter('domain', $value);
    }

    /**
     * @return string
     */
    public function getPaymentType()
    {
        return $this->getParameter('paymentType');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setPaymentType($value)
    {
        return $this->setParameter('paymentType', $value);
    }

    /**
     * @return string
     */
    public function getBrand()
    {
        return $this->getParameter('brand');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setBrand($value)
    {
        return $this->setParameter('brand', $value);
    }

    /**
     * Used for PayPal Express Checkout.
     * @return string
     */
    public function getCommit()
    {
        return $this->getParameter('commit');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setCommit($value)
    {
        return $this->setParameter('commit', $value);
    }

    /**
     * Used for PayPal Express Checkout.
     * @return string
     */
    public function getMode()
    {
        return $this->getParameter('mode');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setMode($value)
    {
        return $this->setParameter('mode', $value);
    }

    /**
     * Used for EPS checkouts.
     * Austrian bank ID.
     * @return string
     */
    public function getBankId()
    {
        return $this->getParameter('bankId');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setBankId($value)
    {
        return $this->setParameter('bankId', $value);
    }

    /**
     * Used for EPS checkouts.
     * @return string
     */
    public function getBic()
    {
        return $this->getParameter('bic');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setBic($value)
    {
        return $this->setParameter('bic', $value);
    }

    /**
     * Used for Direct Debit (ELV) checkouts.
     * @return string
     */
    public function getIban()
    {
        return $this->getParameter('iban');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setIban($value)
    {
        return $this->setParameter('iban', $value);
    }

    /**
     * @return string
     */
    public function getMandateId()
    {
        return $this->getParameter('mandateId');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setMandateId($value)
    {
        return $this->setParameter('mandateId', $value);
    }

    /**
     * @return string
     */
    public function getDateOfSignature()
    {
        return $this->getParameter('dateOfSignature');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setDateOfSignature($value)
    {
        return $this->setParameter('dateOfSignature', $value);
    }
}
