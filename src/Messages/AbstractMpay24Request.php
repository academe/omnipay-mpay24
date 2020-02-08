<?php

namespace Omnipay\Mpay24\Messages;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Mpay24\ParameterTrait;
use Omnipay\Mpay24\ConstantsInterface;
use Mpay24\Mpay24;
use Mpay24\Mpay24Config;

abstract class AbstractMpay24Request extends AbstractRequest implements ConstantsInterface
{
    use ParameterTrait;

    /**
     * Return the Mpay24 SDK object, with all settings configured.
     */
    protected function getMpay()
    {
        $mpay24config = new Mpay24Config();

        $mpay24config->useTestSystem($this->getTestMode());
        $mpay24config->setDebug((bool)$this->getDebug());
        $mpay24config->setMerchantId($this->getMerchantId());
        $mpay24config->setSoapPassword($this->getPassword());

        $mpay24 = new Mpay24($mpay24config);

        return $mpay24;
    }
}
