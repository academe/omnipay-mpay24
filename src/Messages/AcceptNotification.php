<?php

namespace Omnipay\Mpay24\Messages;

/**
 * The notification channel is ths channel safe to accept
 * the results of a transaction. The notifications are not
 * signed. Pairing this push notification with a transaction
 * pull is the safest way to confirm the results.
 */

use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Mpay24\ParameterTrait;
use Omnipay\Mpay24\Messages\AbstractMpay24Request;
use Money\Currency;
use Money\Money;

class AcceptNotification extends AbstractMpay24Request implements NotificationInterface, ResponseInterface 
{
    use ParameterTrait;

    protected $data;

    /**
     * {@inheritdoc}
     */
    public function getTransactionReference()
    {
        return $this->getDataItem('MPAYTID');
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionId()
    {
        return $this->getDataItem('TID');
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionStatus()
    {
        switch ($this->getStatus()) {
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
        return $this->getStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        return $this->getStatus() === static::TRANSACTION_STATE_BILLED
            ||  $this->getStatus() === static::TRANSACTION_STATE_RESERVED;
    }

    /**
     * {@inheritdoc}
     */
    public function isCancelled()
    {
        return $this->getStatus() === static::TRANSACTION_STATE_REVERSED;
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

    /**
     * Also known as transaction state.
     * See https://docs.mpay24.com/docs/transaction-states
     */
    public function getStatus()
    {
        return $this->getDataItem('STATUS');
    }

    public function getDescription()
    {
        return $this->getDataItem('ORDERDESC');
    }

    public function getPaymentSystem()
    {
        return $this->getDataItem('P_TYPE');
    }

    public function getBrand()
    {
        return $this->getDataItem('BRAND');
    }

    public function getUserField()
    {
        return $this->getDataItem('USER_FIELD');
    }

    /**
     * @return int
     */
    public function getAmountMinorUnits()
    {
        return (int)$this->getDataItem('PRICE');
    }

    /**
     * @return int
     */
    public function getCurrencyCode()
    {
        return $this->getDataItem('CURRENCY');
    }

    /**
     * @return Money
     */
    public function getMoney()
    {
        $currency = new Currency($this->getCurrencyCode());

        return new Money($this->getAmountMinorUnits(), $currency);
    }
}
