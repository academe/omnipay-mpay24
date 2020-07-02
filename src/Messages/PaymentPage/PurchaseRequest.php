<?php

namespace Omnipay\Mpay24\Messages\PaymentPage;

use Omnipay\Mpay24\Messages\AbstractMpay24Request;
use Omnipay\Common\Exception\InvalidRequestException;
use Mpay24\Mpay24Order;

class PurchaseRequest extends AbstractMpay24Request
{
    /**
     * The number of payment methods that have been requested.
     */
    protected $paymentMethodCount = 0;

    /**
     * The data key names are from the mPAY24 spec, but lower camelCase.
     *
     * @return array
     * @throws InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        // Defaults for manual override.

        $useProfile = $this->getUseProfile();
        $customerId = $this->getCardReference();

        if ($this->getCreateCard()) {
            // The application would like to create or update a card reference,
            // also known as customerID, for recurrent payments.

            $useProfile = true;

            if (empty($customerId)) {
                //throw new InvalidRequestException('The customerId or cardReference parameter is required');
     
                // Note: we don't want this as it will fill up the account
                // with many random customer IDs, hence throwing the exception..

                // A specified cardReference has not been supplied (which is
                // recommended) so we need to make one up.
                // Format is 1 to 32 characters.

                //$customerId = bin2hex(random_bytes(16));
                $customerId = md5(getmypid() . $this->getTransactionId() . microtime(true));
            }
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
            'useProfile' => (bool)$useProfile,
            'customerId' => $customerId,
            'customerName' => $this->getCustomerName(),
            'billingAddress' => $this->getBillingAddressData(),
            'shippingAddress' => $this->getShippingAddressData(),
            'shoppingCart' => $this->getShoppingCartData(),
        ];
    }

    /**
     * XML Encode all strings in a nested array
     */
    public function xmlEncodeData(array $data): array
    {
        array_walk_recursive($data, function (& $item) {
            if (is_string($item)) {
                $item = str_replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], $item);
            }
        });

        return $data;
    }

    /**
     * @param array $data
     * @return ResponseInterface|PurchaseResponse
     */
    public function sendData($data)
    {
        $data = $this->xmlEncodeData($data);

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

        if (isset($data['paymentType'])) {
            // A single payment type is requested.

            $this->addPaymentType($mdxi, $data['paymentType'], $data['brand'] ?? null);
        }

        if (isset($data['paymentMethods'])) {
            // A list of payment types is requested for the payment page.

            if (is_string($data['paymentMethods'])) {
                $paymentMethods = json_decode($data['paymentMethods'], true);
            } else {
                $paymentMethods = $data['paymentMethods'];
            }

            if (is_array($paymentMethods)) {
                foreach ($paymentMethods as $paymentMethod) {
                    $this->addPaymentType($mdxi, $paymentMethod['paymentType'], $paymentMethod['brand'] ?? null);
                }
            }
        }

        if (! empty($data['description'])) {
            $mdxi->Order->ShoppingCart->Description = $data['description'];
        }

        // Populate the optional basket.

        if (! empty($data['shoppingCart'])) {
            foreach ($data['shoppingCart'] as $itemNumber => $item) {
                $mdxi->Order->ShoppingCart->Item($itemNumber)->Number = $itemNumber;
                $mdxi->Order->ShoppingCart->Item($itemNumber)->ProductNr = $item['productNr'];
                $mdxi->Order->ShoppingCart->Item($itemNumber)->Description = $item['description'];
                //$mdxi->Order->ShoppingCart->Item($itemNumber)->Package = "Package 1";
                $mdxi->Order->ShoppingCart->Item($itemNumber)->Quantity = $item['quantity'];
                $mdxi->Order->ShoppingCart->Item($itemNumber)->ItemPrice = $item['itemPrice'];
                //$mdxi->Order->ShoppingCart->Item($itemNumber)->ItemPrice->setTax(1.23);
                //$mdxi->Order->ShoppingCart->Item($itemNumber)->Price = 10.00;
                // Example os styling. The product on a red background.
                //$mdxi->Order->ShoppingCart->Item($itemNumber)->Description->setStyle('background-color: red');
            }
        }

        $mdxi->Order->Price = $data['price'];

        if (! empty($data['currency'])) {
            $mdxi->Order->Currency = $data['currency'];
        }

        // See for more details:
        // https://docs.mpay24.com/docs/working-with-the-mpay24-php-sdk-redirect-integration
        // Other supported objects (in order): BillingAddr, ShippingAddr

        if (isset($data['customerId'])) {
            $mdxi->Order->Customer->setId($data['customerId']);
        }

        if (isset($data['useProfile'])) {
            $mdxi->Order->Customer->setUseProfile($data['useProfile'] ? 'true' : 'false');
        }

        if (isset($data['customerName'])) {
            $mdxi->Order->Customer = $data['customerName'];
        }

        if (! empty($data['billingAddress'])) {
            $mdxi->Order->BillingAddr->setMode($data['billingAddress']['mode']);
            $mdxi->Order->BillingAddr->Name = $data['billingAddress']['name'];
            //$mdxi->Order->BillingAddr->Gender = $data['billingAddress']['gender'];
            $mdxi->Order->BillingAddr->Street = $data['billingAddress']['street'];
            $mdxi->Order->BillingAddr->Street2 = $data['billingAddress']['street2'];
            $mdxi->Order->BillingAddr->Zip = $data['billingAddress']['zip'];
            $mdxi->Order->BillingAddr->City = $data['billingAddress']['city'];
            $mdxi->Order->BillingAddr->Country->setCode($data['billingAddress']['countryCode']);
            $mdxi->Order->BillingAddr->Email = $data['billingAddress']['email'];
            $mdxi->Order->BillingAddr->Phone = $data['billingAddress']['phone'];
        }

        if (! empty($data['shippingAddress'])) {
            $mdxi->Order->ShippingAddr->setMode($data['shippingAddress']['mode']);
            $mdxi->Order->ShippingAddr->Name = $data['shippingAddress']['name'];
            $mdxi->Order->ShippingAddr->Street = $data['shippingAddress']['street'];
            $mdxi->Order->ShippingAddr->Street2 = $data['shippingAddress']['street2'];
            $mdxi->Order->ShippingAddr->Zip = $data['shippingAddress']['zip'];
            $mdxi->Order->ShippingAddr->City = $data['shippingAddress']['city'];
            $mdxi->Order->ShippingAddr->Country->setCode($data['shippingAddress']['countryCode']);
            $mdxi->Order->ShippingAddr->Phone = $data['shippingAddress']['phone'];
        }

        $mdxi->Order->URL->Success      = $data['successUrl'];
        $mdxi->Order->URL->Error        = $data['errorUrl'];
        $mdxi->Order->URL->Confirmation = $data['confirmationUrl'];

        // The SDK sends the details to the gateway and is given
        // a rediect URL in response.

        //echo '<textarea style="width: 50%; height: 20em;">'; echo $mdxi->toXml(); echo '</textarea>';

        $mpay24 = $this->getMpay();

        $paymentPage = $mpay24->paymentPage($mdxi);

        $data['redirectUrl'] = $paymentPage->getLocation();

        $data['status'] = $paymentPage->getStatus(); // OK or ERROR
        $data['returnCode'] = $paymentPage->getReturnCode();

        return new Response($this, $data);
    }

    /**
     * Add a single payment method to the mdxi object.
     */
    protected function addPaymentType(Mpay24Order $mdxi, string $paymentType, ?string $brand = null)
    {
        if ($this->paymentMethodCount === 0) {
            $mdxi->Order->PaymentTypes->setEnable('true');
        }

        $this->paymentMethodCount++;

        $mdxi->Order->PaymentTypes->Payment($this->paymentMethodCount)->setType($paymentType);

        if ($brand !== null) {
            $mdxi->Order->PaymentTypes->Payment($this->paymentMethodCount)->setBrand($brand);
        }
    }
}
