<?php

namespace Omnipay\Mpay24\Messages;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Mpay24\ConstantsInterface;
use Omnipay\Mpay24\ParameterTrait;

abstract class AbstractMpay24Response extends AbstractResponse implements ConstantsInterface
{
    use ParameterTrait;

    public function getDataItem(string $name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

}
