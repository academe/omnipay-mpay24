<?php

namespace Omnipay\Mpay24\Messages\Backend;

use Omnipay\Mpay24\Messages\AbstractMpay24Request;
use Mpay24\Mpay24Order;

class PurchaseRequest extends AbstractMpay24Request
{
    /**
     * The data key names are from the mPAY24 spec, but lower camelCase.
     *
     * @return array
     * @throws InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        // TODO: validate mandatory params.

        // A single shopping cart item can be provided.
        // Just take the first item from the basket.

        $items = $this->getShoppingCartData();

        if (! empty($items)) {
            $item = array_shift($items);

            $shoppingCart = [
                'item' => [
                    'number' => $item['number'],
                    'productNr' => $item['productNr'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'amount' => $item['amount'],
                ]
            ];
        } else {
            $shoppingCart = [];
        }

        return [
            'tid' => $this->getTransactionId(),
            'payment' => [
                'amount' => $this->getAmountInteger(),
                'currency' => $this->getCurrency(),
                'useProfile' => true,
            ],
            'additional' => [
                'customerID' => $this->getCustomerId() ?: $this->getCardReference(),
                'customerName' => $this->getCustomerName(),
                'order' => [
                    'description' => $this->getDescription(),
                    'shoppingCart' => $shoppingCart,
                ],
                'confirmationUrl' => $this->getNotifyUrl(),
                'language' => strtoupper($this->getLanguage()),
            ],
        ];
    }

    /**
     * @param array $data
     * @return ResponseInterface|PurchaseResponse
     */
    public function sendData($data)
    {
        $mpay24 = $this->getMpay();

        $result = $mpay24->payment(
            static::PTYPE_PROFILE,
            $data['tid'],
            $data['payment'],
            $data['additional']
        );

        $resultData = [
            'operationStatus' => $result->getStatus(),
            'returnCode' => $result->getReturnCode(),
            'errNo' => $result->getErrNo(),
            'errText' => $result->getErrText(),
            'transactionReference' => $result->getMpayTid(),
        ];

        return new Response($this, $resultData);
    }
}
