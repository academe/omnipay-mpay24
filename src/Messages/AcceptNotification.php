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
        NotificationValuesTrait::getPaymentType insteadof ParameterTrait;
        NotificationValuesTrait::getCustomerId insteadof ParameterTrait;
        NotificationValuesTrait::getProfileId insteadof ParameterTrait;
    }

    /**
     * @var string extend statuses of NotificationInterface.
     */
    const STATUS_REFUNDED = 'refunded';

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
            case static::TRANSACTION_STATE_SUSPENDED:
                return static::STATUS_PENDING;

            case static::TRANSACTION_STATE_CREDITED:
                return static::STATUS_REFUNDED;
        }

        return static::STATUS_FAILED;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        // There are no real messages, just codes.
        return $this->getCode();
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
            ||  $this->isPending();
    }

    /**
     * {@inheritdoc}
     */
    public function isPending()
    {
        return $this->getTransactionState() === static::TRANSACTION_STATE_RESERVED
            || $this->getTransactionState() === static::TRANSACTION_STATE_SUSPENDED;
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
     * An extension of the standard Omnipay statuses.
     * @return bool
     */
    public function isRefunded()
    {
        return $this->getTransactionState() === static::TRANSACTION_STATE_CREDITED
            || $this->getTransactionState() === static::TRANSACTION_STATE_REVERSED;

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
