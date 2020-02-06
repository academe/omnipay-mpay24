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

    public function getErrNo()
    {
        return $this->getDataItem('errNo');
    }

    public function getErrText()
    {
        return $this->getDataItem('errText');
    }

    public function getReturnCode()
    {
        return $this->getDataItem('returnCode');
    }

    /**
     * The operation see OPERATION_STATUS_* constants.
     */
    public function getOperationStatus()
    {
        return $this->getDataItem('operationStatus');
    }
}
