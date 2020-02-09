<?php

namespace Omnipay\Mpay24\Messages\PaymentPage;

use Omnipay\Mpay24\Messages\AbstractMpay24Request;
use Mpay24\Mpay24Order;

class PurchaseRequest extends AbstractMpay24Request
{
    /**
     * The number of payment methods that have been requested.
     */
    protected $paymentMethodCount = 0;

    /**
     * @return array
     * @throws InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        $card = $this->getCard();

        if ($card) {
            if ($card->getname()) {
                $customerName = $card->getname();
            }
        } else {
            $customerName = null;
        }

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
            'paymentType' => $this->getPaymentType(),
            'brand' => $this->getBrand(),
            'paymentMethods' => $this->getPaymentMethods(),
            'useProfile' => $this->getUseProfile(),
            'customerId' => $this->getCustomerId(),
            'customerName' => $customerName,
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

        if (! empty($data['templateSet'])) {
            $mdxi->Order->setTemplate = $data['templateSet'];
        }

        if (! empty($data['language'])) {
            $mdxi->Order->TemplateSet->setLanguage($data['language']);
        }

        if (! empty($data['cssName'])) {
            $mdxi->Order->TemplateSet->setCSSName($data['cssName']);
        }

        if (isset($data['paymentType']) && isset($data['brand'])) {
            // A single payment type is requested.

            $this->addPaymentType($mdxi, $data['paymentType'], $data['brand']);
        }

        if (isset($data['paymentMethods'])) {
            // A list of payment types is requested for the payment page.

            $paymentMethods = json_decode($data['paymentMethods'], true);

            if (is_array($paymentMethods)) {
                foreach ($paymentMethods as $paymentMethod) {
                    $this->addPaymentType($mdxi, $paymentMethod['paymentType'], $paymentMethod['brand']);
                }
            }
        }

        if (! empty($data['description'])) {
            $mdxi->Order->ShoppingCart->Description = $data['description'];
        }

        if (! empty($this->getItems())) {
            $itemNumber = 0;

            foreach ($this->getItems() as $item) {
                $itemNumber++;

                $mdxi->Order->ShoppingCart->Item($itemNumber)->Number = $itemNumber;
                $mdxi->Order->ShoppingCart->Item($itemNumber)->ProductNr = $item->getName();
                $mdxi->Order->ShoppingCart->Item($itemNumber)->Description = $item->getDescription() ?: $item->getName();
                //$mdxi->Order->ShoppingCart->Item($itemNumber)->Package = "Package 1";
                $mdxi->Order->ShoppingCart->Item($itemNumber)->Quantity = $item->getQuantity();
                $mdxi->Order->ShoppingCart->Item($itemNumber)->ItemPrice = $item->getPrice();
                //$mdxi->Order->ShoppingCart->Item($itemNumber)->ItemPrice->setTax(1.23);
                //$mdxi->Order->ShoppingCart->Item($itemNumber)->Price = 10.00;
            }
        }

        $mdxi->Order->Price = $data['price'];

        if (! empty($data['currency'])) {
            $mdxi->Order->Currency = $data['currency'];
        }

        // See for more details:
        // https://docs.mpay24.com/docs/working-with-the-mpay24-php-sdk-redirect-integration
        // Other supported objects (in order): Customer, BillingAddr, ShippingAddr

        if (isset($data['useProfile'])) {
            $mdxi->Order->Customer->setUseProfile($data['useProfile'] ? 'true' : 'false');
        }

        if (isset($data['customerId'])) {
            $mdxi->Order->Customer->setId($data['customerId']);
        }

        if (isset($data['customerName'])) {
            $mdxi->Order->Customer = $data['customerName'];
        }

        $mdxi->Order->URL->Success      = $data['successUrl'];
        $mdxi->Order->URL->Error        = $data['errorUrl'];
        $mdxi->Order->URL->Confirmation = $data['confirmationUrl'];

        // The SDK sends the details to the gateway and is given
        // a rediect URL in response.

        //echo '<textarea style="width: 50%; height: 20em;">'; echo $mdxi->toXml(); echo '</textarea>';

        $paymentPage = $mpay24->paymentPage($mdxi);

        $data['redirectUrl'] = $paymentPage->getLocation();

        $data['status'] = $paymentPage->getStatus(); // OK or ERROR
        $data['returnCode'] = $paymentPage->getReturnCode();

        return new Response($this, $data);
    }

    protected function addPaymentType(Mpay24Order $mdxi, string $paymentType, string $brand)
    {
        if ($this->paymentMethodCount === 0) {
            $mdxi->Order->PaymentTypes->setEnable('true');
        }

        $this->paymentMethodCount++;

        $mdxi->Order->PaymentTypes->Payment($this->paymentMethodCount)->setType($paymentType);
        $mdxi->Order->PaymentTypes->Payment($this->paymentMethodCount)->setBrand($brand);
    }
}
