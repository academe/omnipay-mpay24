<?php

namespace Omnipay\Mpay24\Messages;

/**
 * The notification channel is ths channel safe to accept
 * the results of a transaction. The notifications are not
 * signed. Pairing this push notification with a transaction
 * pull is the safest way to confirm the results.
 *
 * TODO: handle the response code back to mPAY24 - "OK" or "ERROR".
 * See https://docs.mpay24.com/docs/payment-notification
 */

use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Mpay24\ParameterTrait;
use Omnipay\Mpay24\Messages\AbstractMpay24Request;
use Money\Currency;
use Money\Money;

class AcceptNotification extends AbstractMpay24Request implements NotificationInterface, ResponseInterface 
{
    use ParameterTrait, NotificationValuesTrait {
        NotificationValuesTrait::getUserField insteadof ParameterTrait;
        NotificationValuesTrait::getLanguage insteadof ParameterTrait;
    }

    protected $data;

    /**
     * {@inheritdoc}
     */
    public function getTransactionStatus()
    {
        switch ($this->getTransactionState()) {
            case static::TRANSACTION_STATE_BILLED:
                return static::STATUS_COMPLETED;
            case static::TRANSACTION_STATE_RESERVED:
                return static::STATUS_PENDING;
        }

        return static::STATUS_FAILED;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getTransactionState();
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        return $this->getTransactionState() === static::TRANSACTION_STATE_BILLED
            ||  $this->getTransactionState() === static::TRANSACTION_STATE_RESERVED;
    }

    /**
     * {@inheritdoc}
     */
    public function isCancelled()
    {
        return $this->getTransactionState() === static::TRANSACTION_STATE_REVERSED;
    }

    /**
     * {@inheritdoc}
     */
    public function isRedirect()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->httpRequest;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->getRequest()->query->all();
    }

    /**
     * The acceptNotification does not need to be "sent", but if it
     * is, then you will simply get the same object back.
     *
     * {@inheritdoc}
     */
    public function sendData($data)
    {
        return $this;
    }

    public function getDataItem(string $name)
    {
        $this->data = $this->data ?: $this->getData();

        return isset($this->data[$name]) ? $this->data[$name] : null;
    }
}
