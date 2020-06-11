<?php

namespace Omnipay\Mpay24\Messages;

/**
 * Data provided is the raw data from the API, with upper-case keys.
 * TODO: this is also known as the "transaction response", and is shared
 * by notifications, payments, capture, fetchTransaction etc.
 * Some refactoring may help simplify.
 */

use Omnipay\Mpay24\Messages\AbstractMpay24Response;
use Money\Money;
use Money\Currency;

trait NotificationValuesTrait
{
    public function getOperationStatus()
    {
        return $this->getDataItem('operationStatus');
    }

    public function getReturnCode()
    {
        return $this->getDataItem('returnCode');
    }

    public function getCode()
    {
        return $this->getReturnCode();
    }

    /**
     * Transaction ID returned by mPAY24
     * {@inheritdoc}
     */
    public function getTransactionReference()
    {
        return $this->getDataItem('MPAYTID');
    }

    /**
     * Merchant transaction ID
     * {@inheritdoc}
     */
    public function getTransactionId()
    {
        return $this->getDataItem('TID');
    }

    public function getOperation()
    {
        return $this->getDataItem('OPERATION');
    }

    /**
     * Transaction status
     * Also known as transaction state.
     * See https://docs.mpay24.com/docs/transaction-states
     */
    public function getTransactionState()
    {
        return $this->getDataItem('STATUS');
    }

    /**
     * Amount of the transaction in cent (aka minor units)
     * @return int
     */
    public function getAmountMinorUnits()
    {
        return (int)$this->getDataItem('PRICE');
    }

    /**
     * @return string Used currency ISO code
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

    // Used payment system
    // TODO: lists as constants
    public function getPaymentType()
    {
        return $this->getDataItem('P_TYPE');
    }

    // Used brand of the payment system
    // TODO: lists as constants
    public function getPaymentBrand()
    {
        return $this->getDataItem('BRAND');
    }

    // Content of the user field
    public function getUserField()
    {
        return $this->getDataItem('USER_FIELD');
    }

    // Description of the order
    public function getDescription()
    {
        return $this->getDataItem('ORDERDESC');
    }

    // Customer name
    public function getCustomerName()
    {
        return $this->getDataItem('CUSTOMER');
    }

    // Customer e-mail address
    public function getCustomerEmail()
    {
        return $this->getDataItem('CUSTOMER_EMAIL');
    }

    // Merchant customer ID
    public function getCustomerId()
    {
        return $this->getDataItem('CUSTOMER_ID');
    }

    public function getProfileId()
    {
        return $this->getDataItem('PROFILE_ID');
    }

    public function getCardReference()
    {
        return $this->getCustomerId();
    }

    // Status of the customer profile
    // TODO: lists as constants
    public function getProfileStatus()
    {
        return $this->getDataItem('PROFILE_STATUS');
    }

    // Used language ISO code (upper case)
    public function getLanguage()
    {
        return $this->getDataItem('LANGUAGE');
    }

    // Approval code returned by the financial institution
    public function getApprovalCode()
    {
        return $this->getDataItem('APPR_CODE');
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        return $this->getOperation() === 'CONFIRMATION' && $this->getTransactionState() === static::TRANSACTION_STATE_BILLED
            || $this->getReturnCode() === 'PROFILE_USED' && $this->getOperationStatus() === static::OPERATION_STATUS_OK;
    }

    /**
     * {@inheritdoc}
     */
    public function isPending()
    {
        return $this->getTransactionState() === static::TRANSACTION_STATE_RESERVED;
    }

    public function isProfileCreated()
    {
        return $this->getProfileStatus() === static::PROFILE_STATUS_CREATED;
    }

    public function isProfileUpdated()
    {
        return $this->getProfileStatus() === static::PROFILE_STATUS_UPDATED;
    }

    public function isProfileDeleted()
    {
        return $this->getProfileStatus() === static::PROFILE_STATUS_DELETED;
    }

    public function isProfileChanged()
    {
        return $this->isProfileCreated() || $this->isProfileUpdated() || isProfileDeleted();
    }
}
