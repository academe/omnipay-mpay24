<?php

namespace Omnipay\Mpay24;

interface ConstantsInterface
{
    /**
     * Overall status of the operation.
     */

    // The operation was ok (no error occurred).
    const OPERATION_STATUS_OK = 'OK';

    // The operation was not ok (see returnCode for more information).
    const OPERATION_STATUS_ERROR = 'ERROR';

    /**
     * For notification handler.
     */

    const NOTIFICATION_RESPONSE_OK      = 'OK';
    const NOTIFICATION_RESPONSE_ERROR   = 'ERROR';

    /**
     * Transaction states.
     * See https://docs.mpay24.com/docs/transaction-states
     */

    // The transaction failed upon the last request.
    // (e.g. wrong/invalid data, financial reasons, ...)
    const TRANSACTION_STATE_ERROR = 'ERROR';
    // The amount was reserved but not settled/billed yet.
    // The transaction was successful.
    const TRANSACTION_STATE_RESERVED = 'RESERVED';
    // The reserved amount was complaint.
    const TRANSACTION_STATE_RESERVED_REVERSAL = 'RESERVED_REVERSAL';
    // The amount was settled/billed.
    // The transaction was successful.
    const TRANSACTION_STATE_BILLED = 'BILLED';
    // The amount was complaint (chargeback).
    // Please get in touch with the customer.
    const TRANSACTION_STATE_BILLED_REVERSAL = 'BILLED_REVERSAL';
    // The reserved amount was released.
    // The transaction was canceled.
    const TRANSACTION_STATE_REVERSED = 'REVERSED';
    // The amount will be refunded.
    // The transaction was credited.
    const TRANSACTION_STATE_CREDITED = 'CREDITED';
    // The credited amount was complaint.
    const TRANSACTION_STATE_CREDITED_REVERSAL = 'CREDITED_REVERSAL';
    // Expecting external interface confirmation.
    // The transaction is suspended temporarily.
    const TRANSACTION_STATE_SUSPENDED = 'SUSPENDED';
    // The payout was successful.
    // The amount will be transfered to the customer.
    const TRANSACTION_STATE_WITHDRAWN = 'WITHDRAWN';
    // Other states documented in othee places:
    const TRANSACTION_STATE_INIT = 'INIT';
    const TRANSACTION_STATE_AUTHORIZE = 'AUTHORIZE';
    const TRANSACTION_STATE_REDIRECTED = 'REDIRECTED';
    const TRANSACTION_STATE_CALLBACK = 'CALLBACK';
    const TRANSACTION_STATE_EXECUTE = 'EXECUTE';
    const TRANSACTION_STATE_REVOKE = 'REVOKE';
    const TRANSACTION_STATE_REJECT = 'REJECT';
    const TRANSACTION_STATE_ARCHIVED = 'ARCHIVED';
    const TRANSACTION_STATE_WITHDRAW = 'WITHDRAW';

    /**
     * Return codes to instructions.
     */

    // A redirect is required.
    const RETURN_CODE_REDIRECT = 'REDIRECT';
    // The transaction was declined by the external payment interface.
    const RETURN_CODE_OK = 'DECLINED';
    // The transaction was blocked.
    const RETURN_CODE_BLOCKED = 'BLOCKED';
    // The merchant’s IP address is not white listed.
    const RETURN_CODE_ACCESS_DENIED = 'ACCESS_DENIED';
    // The merchant id is locked.
    const RETURN_CODE_MERCHANT_LOCKED = 'MERCHANT_LOCKED';
    // The desired payment system is not active.
    const RETURN_CODE_PAYMENT_METHOD_NOT_ACTIVE = 'PAYMENT_METHOD_NOT_ACTIVE';
    // A mandatory parameter is missing.
    const RETURN_CODE_NOT_ENTERED = '<parameter>_NOT_ENTERED';
    // The parameter supplied is not valid.
    const RETURN_CODE_NOT_CORRECT = '<parameter>_NOT_CORRECT';
    // The operation is not supported.
    const RETURN_CODE_OPERATION_NOT_SUPPORTED = 'OPERATION_NOT_SUPPORTED';
    // The payment method mismatches.
    const RETURN_CODE_PTYPE_MISMATCH = 'PTYPE_MISMATCH';
    // The transaction was not found.
    const RETURN_CODE_NOT_FOUND = 'NOT_FOUND';
    // The transaction has been processed and can not be processed const RETURN_CODE_OK = 'again.
    const RETURN_CODE_ALREADY_PROCESSED = 'ALREADY_PROCESSED';
    // The temporary cache data are invalid due to expiration.
    const RETURN_CODE_CACHE_DATA_EXPIRED = 'CACHE_DATA_EXPIRED';
    // The MDXI XML stream could not be validated.
    const RETURN_CODE_INVALID_MDXI = 'INVALID_MDXI';
    // The parameter price \ amount holds invalid values.
    const RETURN_CODE_INVALID_PRICE = 'INVALID_PRICE';
    const RETURN_CODE_INVALID_AMOUNT = 'INVALID_AMOUNT';
    // The credit card number is not plausible.
    const RETURN_CODE_INVALID_CREDITCARD_NUMBER = 'INVALID_CREDITCARD_NUMBER';
    // The Maestro card number is not plausible.
    const RETURN_CODE_INVALID_MAESTRO_NUMBER = 'INVALID_MAESTRO_NUMBER';
    // The provided IBAN is not plausible.
    const RETURN_CODE_INVALID_IBAN = 'INVALID_IBAN';
    // mPAY24 payment profile could not be found.
    const RETURN_CODE_PROFILE_NOT_FOUND = 'PROFILE_NOT_FOUND';
    // mPAY24 Profile is not activated.
    const RETURN_CODE_PROFILE_NOT_SUPPORTED = 'PROFILE_NOT_SUPPORTED';
    // mPAY24 Profile FLEX is not activated.
    const RETURN_CODE_PROFILE_FLEX_NOT_SUPPORTED = 'PROFILE_FLEX_NOT_SUPPORTED';
    // The maximum number of payment profiles of a customer is const RETURN_CODE_OK = 'reached.
    const RETURN_CODE_PROFILE_COUNT_EXCEEDED = 'PROFILE_COUNT_EXCEEDED';
    // The token has not been found or has already been used.
    const RETURN_CODE_TOKEN_NOT_FOUND = 'TOKEN_NOT_FOUND';
    // The token is invalid due to missing or incorrect data.
    const RETURN_CODE_TOKEN_NOT_VALID = 'TOKEN_NOT_VALID';
    // The token has not been used and is expired.
    const RETURN_CODE_TOKEN_EXPIRED = 'TOKEN_EXPIRED';
    // The token data could not be encrypted.
    const RETURN_CODE_TOKEN_ENCRYPTION_FAILURE = 'TOKEN_ENCRYPTION_FAILURE';
    // The token data could not be decrypted.
    const RETURN_CODE_TOKEN_DECRYPTION_FAILURE = 'TOKEN_DECRYPTION_FAILURE';
    // Withdraw operation is not allowed for the merchant.
    const RETURN_CODE_WITHDRAW_NOT_ALLOWED = 'WITHDRAW_NOT_ALLOWED';
    // The transaction has already been cleared.
    const RETURN_CODE_TRANSACTION_ALREADY_CLEARED = 'TRANSACTION_ALREADY_CLEARED';
    // The total amount of all credits exceeds the clearing const RETURN_CODE_OK = 'amount.
    const RETURN_CODE_CREDIT_LIMIT_EXCEEDED = 'CREDIT_LIMIT_EXCEEDED';
    // The total amount of all clearing exceeds the const RETURN_CODE_OK = 'reservation amount.
    const RETURN_CODE_CLEARING_LIMIT_EXCEEDED = 'CLEARING_LIMIT_EXCEEDED';
    // An error during the communication occurred.
    const RETURN_CODE_INTERNAL_ERROR = 'INTERNAL_ERROR';
    // The external payment interface returned an error.
    const RETURN_CODE_EXTERNAL_ERROR = 'EXTERNAL_ERROR';

    // For profiles.

    // The transaction was ok and the profile created
    const RETURN_CODE_PROFILE_CREATED = 'PROFILE_CREATED';
    //The transaction was ok and the profile updated
    const RETURN_CODE_PROFILE_UPDATED = 'PROFILE_UPDATED';
    //The transaction was ok but the profile was not stored/updated
    const RETURN_CODE_PROFILE_ERROR = 'PROFILE_ERROR';

    /**
     * Payment systems, aka p-type, or payment type.
     */

    const PTYPE_TOKEN       = 'TOKEN';
    const PTYPE_CC          = 'CC';
    const PTYPE_MAESTRO     = 'MAESTRO';
    const PTYPE_EPS         = 'EPS';
    const PTYPE_GIROPAY     = 'GIROPAY';
    const PTYPE_ELV         = 'ELV';
    const PTYPE_PAYPAL      = 'PAYPAL';
    const PTYPE_PB          = 'PB';
    const PTYPE_QUICK       = 'QUICK';
    const PTYPE_PSC         = 'PSC';
    const PTYPE_BILLPAY     = 'BILLPAY';
    const PTYPE_KLARNA      = 'KLARNA';
    const PTYPE_PAYOLUTION  = 'PAYOLUTION';
    const PTYPE_SOFORT      = 'SOFORT';
    const PTYPE_MASTERPASS  = 'MASTERPASS';
    const PTYPE_PROFILE     = 'PROFILE';

    /**
     * Brands.
     */

    // Hire purchase for BILLPAY and KLARNA
    const BRAND_HP          = 'HP';
    const BRAND_INVOICE     = 'INVOICE';
    // For CC
    const BRAND_AMEX        = 'AMEX';
    const BRAND_DINERS      = 'DINERS';
    const BRAND_JCB         = 'JCB';
    const BRAND_VISA        = 'VISA';
    const BRAND_MASTERCARD  = 'MASTERCARD';
    // For ELV (direct debit)
    const BRAND_ATOS        = 'ATOS';
    const BRAND_HOBEX_AT    = 'HOBEX-AT';
    const BRAND_HOBEX_DE    = 'HOBEX-DE';
    const BRAND_HOBEX_NL    = 'HOBEX-NL';
    const BRAND_BILLPAY     = 'BILLPAY';
    const BRAND_HEIDELPAY   = 'HEIDELPAY';
    const BRAND_B4P         = 'B4P';
    // For EPS. EPS for Austrian banks
    const BRAND_EPS         = 'EPS';
    const BRAND_INTERNATIONAL   = 'INTERNATIONAL';
    // For PSC (redirect payment systems)
    const BRAND_PSC         = 'PSC';
    const BRAND_QUICK       = 'QUICK';
    // Sofort.
    const BRAND_SOFORT      = 'SOFORT';

    /**
     * Address fields modification mode.
     * Note: the WSDL has these as upper case, but that is rejected
     * by the test account. Only mixed case is accepted.
     */

    const ADDRESS_MODE_READWRITE = 'ReadWrite';
    const ADDRESS_MODE_READONLY = 'ReadOnly';


    /**
     * Person gender
     */

    const GENDER_MALE = 'MALE';
    const GENDER_FEMALE = 'FEMALE';

    /**
     * Example CSS Names.
     * Use CSS_NAME_MODERN as the current responsive template.
     */

    const CSS_NAME_WEB      = 'WEB';
    const CSS_NAME_MOBILE   = 'MOBILE';
    const CSS_NAME_MODERN   = 'MODERN';

    /**
     *
     */

    const PROFILE_STATUS_IGNORED    = 'IGNORED';
    const PROFILE_STATUS_USED       = 'USED';
    const PROFILE_STATUS_ERROR      = 'ERROR';
    const PROFILE_STATUS_CREATED    = 'CREATED';
    const PROFILE_STATUS_UPDATED    = 'UPDATED';
    const PROFILE_STATUS_DELETED    = 'DELETED';
}
