<?php

namespace Omnipay\Mpay24\Messages\Redirect;

use Omnipay\Mpay24\Messages\AbstractMpay24Request;
use Mpay24\Mpay24Order;

class PurchaseRequest extends AbstractMpay24Request
{
    /**
     * @return array
     * @throws InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        return [
            'price' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'tid' => $this->getTransactionId(),
            'description' => $this->getDescription(),
            'language' => strtoupper($this->getLanguage()),
            'templateSet' => $this->getTemplateSet(),
            'successUrl' => $this->getReturnUrl(),
            'errorUrl' => $this->getErrorUrl() ?: $this->getReturnUrl(),
            'confirmationUrl' => $this->getNotifyUrl(),
        ];
    }

    /**
     * @param array $data
     * @return ResponseInterface|PurchaseResponse
     */
    public function sendData($data)
    {
        $mpay24 = $this->getMpay();

        $mdxi = new Mpay24Order();
        $mdxi->Order->Tid = $data['tid'];
        $mdxi->Order->Price = $data['price'];

        if (! empty($data['currency'])) {
            $mdxi->Order->Currency = $data['currency'];
        }

        if (! empty($data['templateSet'])) {
            $mdxi->Order->TemplateSet = $data['templateSet'];
        }

        if (! empty($data['cssName'])) {
            $mdxi->Order->setCSSName($data['cssName']);
        }

        // Only used if payment types need to be pre-selected.
        // Not implemented, but example below.
        // $mdxi->Order->PaymentTypes->setEnable('true');
        // $mdxi->Order->PaymentTypes->Payment(1)->setType('CC');
        // $mdxi->Order->PaymentTypes->Payment(1)->setBrand('VISA');

        // See for more details:
        // https://docs.mpay24.com/docs/working-with-the-mpay24-php-sdk-redirect-integration

        $mdxi->Order->URL->Success      = $data['successUrl'];
        $mdxi->Order->URL->Error        = $data['errorUrl'];
        $mdxi->Order->URL->Confirmation = $data['confirmationUrl'];

        // Other expected fields: Customer, BillingAddr, ShippingAddr

        // The SDK sends the details to the gateway and is given
        // a rediect URL in response.

        $paymentPage = $mpay24->paymentPage($mdxi);

        $data['redirectUrl'] = $paymentPage->getLocation();

        $data['status'] = $paymentPage->getStatus(); // OK or ERROR
        $data['returnCode'] = $paymentPage->getReturnCode();

        return new Response($this, $data);
    }
}
