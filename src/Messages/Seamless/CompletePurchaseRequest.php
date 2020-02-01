<?php

namespace Omnipay\Mpay24\Messages\Seamless;

// array(5) { ["result"]=> string(7) "success" ["TID"]=> string(7) "txn-123" ["LANGUAGE"]=> string(2) "EN" ["USER_FIELD"]=> string(0) "" ["BRAND"]=> string(4) "VISA" }
// array(7) { ["result"]=> string(5) "error" ["TID"]=> string(7) "txn-123" ["USER_FIELD"]=> string(0) "" ["ERRTEXT"]=> string(0) "" ["EXTERNALSTATUS"]=> string(0) "" ["BRAND"]=> string(4) "VISA" ["LANGUAGE"]=> string(2) "EN" } 

use Omnipay\Mpay24\Messages\AbstractMpay24Request;
use Omnipay\Mpay24\Messages\FetchTransactionResponse;
use Mpay24\Mpay24Order;

class CompletePurchaseRequest extends AbstractMpay24Request
{
    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        // The result will be passed by query parameters, and that may be useful
        // for displaying a message on the front-end. However, that cannot be
        // trused.
        // A notification will be sent to the merchant site (a push result),
        // but we can at this point perform a pull request to get the result of
        // the transaction.

        return []; // txn id and maybe tx ref?
    }

    /**
     * @return array
     * @throws PurchaseReponse
     */
    public function sendData($data)
    {
// TODO: move this to the fetchTransactionRequest
        $mpay24 = $this->getMpay();

        $result = $mpay24->paymentStatusByTID($this->getTransactionId());
/*
array(18) {
  ["OPERATION"]=>
  string(12) "CONFIRMATION"
  ["TID"]=>
  string(7) "txn-123"
  ["STATUS"]=>
  string(5) "ERROR"
  ["PRICE"]=>
  string(3) "990"
  ["CURRENCY"]=>
  string(3) "EUR"
  ["P_TYPE"]=>
  string(2) "CC"
  ["BRAND"]=>
  string(4) "VISA"
  ["MPAYTID"]=>
  string(7) "7971316"
  ["USER_FIELD"]=>
  string(0) ""
  ["ORDERDESC"]=>
  string(10) "Test Order"
  ["CUSTOMER"]=>
  string(7) "Jon Doe"
  ["CUSTOMER_EMAIL"]=>
  string(0) ""
  ["LANGUAGE"]=>
  string(2) "EN"
  ["CUSTOMER_ID"]=>
  string(11) "customer123"
  ["PROFILE_ID"]=>
  string(0) ""
  ["PROFILE_STATUS"]=>
  string(7) "IGNORED"
  ["FILTER_STATUS"]=>
  string(0) ""
  ["APPR_CODE"]=>
  string(0) ""
}
*/
        $params = $result->getParams();

        $params['operationStatus'] = $result->getStatus();
        $params['returnCode'] = $result->getReturnCode();

        return new FetchTransactionResponse($this, $params);
    }
}
