<?php

namespace Omnipay\Mpay24\Messages;

/**
 * Data provided is the raw data from the API, with upper-case keys.
 */

use Omnipay\Mpay24\Messages\AbstractMpay24Response;

class PaymentMethodsResponse extends AbstractMpay24Response
{
    use NotificationValuesTrait;

    public function getPaymentTypes()
    {
        return $this->getDataItem('paymentTypes') ?: [];
    }

    public function getBrands()
    {
        return $this->getDataItem('brands') ?: [];
    }

    public function getDescriptions()
    {
        return $this->getDataItem('descriptions') ?: [];
    }

    public function getPaymentMethodIds()
    {
        return $this->getDataItem('paymentMethodIds') ?: [];
    }

    public function getAll()
    {
        $count = count($this->getPaymentTypes());

        $all = [];

        $paymentTypes = $this->getPaymentTypes();
        $brands = $this->getBrands();
        $descriptions = $this->getDescriptions();
        $paymentMethodIds = $this->getPaymentMethodIds();

        for ($i = 0; $i < $count; $i++) {
            $all[$i] = [
                'paymentType' => $paymentTypes[$i] ?? null,
                'brand' => $brands[$i] ?? null,
                'description' => $descriptions[$i] ?? null,
                'paymentMethodId' => $paymentMethodIds[$i] ?? null,
            ];
        }

        return $all;
    }
}
