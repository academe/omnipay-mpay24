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

        return [
            'transactionId' => $this->getTransactionId(),
            'transactionReference' => $this->getTransactionReference(),
        ];
    }

    /**
     * @return array
     * @throws PurchaseReponse
     */
    public function sendData($data)
    {
        $mpay24 = $this->getMpay();

        if (! empty($data['transactionId'])) {
            $result = $mpay24->paymentStatusByTID($data['transactionId']);
        }

        if (! empty($data['transactionReference'])) {
            $result = $mpay24->paymentStatus($data['transactionReference']);
        }

        $params = $result->getParams();

        $params['operationStatus'] = $result->getStatus();
        $params['returnCode'] = $result->getReturnCode();

        return new FetchTransactionResponse($this, $params);
    }
}
