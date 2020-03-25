
[![Latest Stable Version](https://poser.pugx.org/academe/omnipay-mpay24/v/stable)](https://packagist.org/packages/academe/omnipay-mpay24)
[![Total Downloads](https://poser.pugx.org/academe/omnipay-mpay24/downloads)](https://packagist.org/packages/academe/omnipay-mpay24)
[![Latest Unstable Version](https://poser.pugx.org/academe/omnipay-mpay24/v/unstable)](https://packagist.org/packages/academe/omnipay-mpay24)
[![License](https://poser.pugx.org/academe/omnipay-mpay24/license)](https://packagist.org/packages/academe/omnipay-mpay24)

Table of Contents
=================

   * [Table of Contents](#table-of-contents)
   * [mPAY24 Driver for Omnipay v3](#mpay24-driver-for-omnipay-v3)
      * [Seamless Payment Initiation](#seamless-payment-initiation)
         * [Create Token](#create-token)
         * [Payment Using Token](#payment-using-token)
         * [Seamless Complete Payment](#seamless-complete-payment)
      * [Payment Page](#payment-page)
         * [Purchase (redirect)](#purchase-redirect)
         * [Payment Page Complete Payment](#payment-page-complete-payment)
      * [Notification Handler](#notification-handler)

# mPAY24 Driver for Omnipay v3

There are two main front ends to initiate a payment: *paymentPage* and *seamless*.

The *paymentPage* (also known as the *redirect* method) handles payments completely
offsite, while *seamless* keeps the user on site for most of the time, only going off
site for 3D Secure or remote service authentication and authorisation.

Both intiation types use the same direct server (known as *backend2backend*) API methods.

## Seamless Payment Initiation

This intiation method handles a number of payment types, some requiring additional
PCI checks to use.
The most comming credit card method will be token based, with a token being created
at the back end first, and a URL related to that token being used to provide an
iframe-based credit card form.
An example of how this can work is shown below, but there are other ways it can be
done, with additional front-end functionality to choose payment types.

### Create Token

First a token is created on the back end.
This token will need to be saved for the next stage, either in the session or passed
through the order form.

```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Mpay24_Seamless');

$gateway->setMerchantId('12345');
$gateway->setPassword('AB1234cd56');
$gateway->setTestMode(true);

$request = $gateway->token([
    'language' => 'en',
    //'customerId' => 'foo',
    //'profileId' => 'bar',
    //'style' => 'fizz',
]);

$response = $request->send();

if (! $response->isSuccessful()) {
    // Token could not be generated.
    echo '<p>Error: '.$response->getReturnCode().'</p>';
    exit;
}
```

This gives us a token and an iframe URL:

```php
$response->getRedirectUrl();
$response->getToken();
```

The payment form can be created as follows, assuming `/pay` as the next enpoint in your flow.
The iframe will contain the rendered credit card form.
Add whatever additional customer or order details you want to the form.
The iframe will be submitted with the form, but won't itself make any changes
to your form; the credit card details go straight to the mPAY24 gateway.
With this example, the submit bitton will remain disabled until the credit card
details in the iframe have been completed.

The token does not need to go through the form,
but could be carried forward through the session instead.

```php
<?php

<iframe src="<?php echo $response->getRedirectUrl(); ?>" frameBorder="0" width="500"></iframe>

<form action="/pay" method="POST">
  <input name="token" type="hidden" value="<?php echo $response->getToken(); ?>" />
  <button id="paybutton" name="type" value="TOKEN" type="submit" disabled="true">Pay with creditcard</button>
  <button name="type" value="PAYPAL" type="submit">Pay with paypal</button>
</form>

<script>
  window.addEventListener("message", checkValid, false);
  function checkValid(form) {
    var data = JSON.parse(form.data);
    if (data.valid === "true") {
      document.getElementById("paybutton").disabled=false;
    }
  }
</script>
```

The `/pay` endpoint handles the actual payment.

### Payment Using Token

```php
use Omnipay\Omnipay;
use Omnipay\Common\CreditCard;

$gateway = Omnipay::create('Mpay24_Seamless');

$gateway->setMerchantId('12345');
$gateway->setPassword('AB1234cd56');
$gateway->setTestMode(true);

$card = new CreditCard([
    'name' => 'Fred Bloggs',
    //
    'billingName' => 'Fred Billing',
    'billingAddress1' => 'Street 1',
    'billingAddress2' => 'Street 2',
    'billingCity' => 'City',
    'billingPostcode' => 'Postcode',
    'billingCountry' => 'GB',
    //
    'shippingName' => 'Fred Shipping',
    'shippingAddress1' => 'Street 1',
    'shippingAddress2' => 'Street 2',
    'shippingCity' => 'City',
    'shippingPostcode' => 'Postcode',
    'shippingCountry' => 'GB',
]);

$request = $gateway->purchase([
    'paymentType' => 'TOKEN', // or PAYPAL etc. e.g $_POST['type'] in this example.
    'amount' => '9.98',
    'currency' => 'EUR',
    'token' => $token, // e.g. $_POST['token']
    'transactionId' => $transactionId,
    'description' => 'Test Order',
    'returnUrl' => 'https://example.com/complete/success',
    'errorUrl' => 'https://example.com/complete/error',
    'notifyUrl' => 'https://example.com/notify',
    'language' => 'en',
    'card' => $card,
]);

$response = $request->send();
```

If the payment request is succcessful, then a redirect is likely
to be needed to complete 3D Secure actions, Paypal or bank authentication
and so on:

```php
if (! $response->isSuccessful() && $response->isRedirect()) {
    $response->redirect();
    exit;
}
```

### Seamless Complete Payment

After 3D Secure is completed, you will be returned to your `/complete` endpoint
where you need to fetch the results of the transation:

```
use Omnipay\Omnipay;

$gateway = Omnipay::create('Mpay24_Seamless');

$gateway->setMerchantId('12345');
$gateway->setPassword('AB1234cd56');
$gateway->setTestMode(true);

$request = $gateway->completePurchase([
    // Will be in $_GET['TID'], but don't trust that; store it in the session.
    'transactionId' => $transactionId,
]);

$response = $request->send();
```

The `$response` will contain the normal Omnipay statuses and messages to define
the result.

**Note:** your `complete` endpoint will be given the transaction result when redirected
from the gateway.
This result is *not* signed, and so can be easily manipulated by an end user.
For this reason, this driver fetches the result from the gateway (a "pull" notification)
to ensure no untrusted user data becomes a part of the process.

## Payment Page

The payment page sends the user to the payment gateway to make a payment.
The user will have a single payment type chosen for them, or can choose
from a range of payment types offered, from a list filtered by the
merchant site.

### Purchase (redirect)

```php
use Omnipay\Omnipay;
use Omnipay\Common\CreditCard;

$gateway = Omnipay::create('Mpay24_PaymentPage');

$gateway->setMerchantId('12345');
$gateway->setPassword('AB1234cd56');
$gateway->setTestMode(true);

$request = $gateway->purchase([
    'amount' => '9.98',
    'currency' => 'EUR',
    'token' => $token, // e.g. $_POST['token']
    'transactionId' => $transactionId,
    'description' => 'Test Order',
    'returnUrl' => 'https://example.com/complete/success',
    'errorUrl' => 'https://example.com/complete/error',
    'notifyUrl' => 'https://example.com/notify',
    'language' => 'en',
    'card' => $card, // Names, addresses
    'items' => $items,
]);

$response = $request->send();
```

If all is accepted, the `$response` object will be a redirect to
the payment page.

To restrict the user to a single payment method, add the `paymentType`
and `brand`. Example:

```php
    'paymentType' => 'CC',
    'brand' => 'VISA',
```

Alternatively a range of payment methods can be supplied as a JSON string:

```php
    'paymentMethods' => '[{"paymentType":"CC","brand":"VISA"},{"paymentType":"CC","brand":"MASTERCARD"},{"paymentType":"PAYPAL","brand":"PAYPAL"}]',

    // alternatively:

    'paymentMethods' => json_encode([
        ['paymentType' => 'CC', 'brand' => 'VISA'],
        ['paymentType' => 'CC', 'brand' => 'MASTERCARD'],
        ['paymentType' => 'PAYPAL', 'brand' => 'PAYPAL'],
    ]),
```

### Payment Page Complete Payment

The transaction is completed in exactly the same way as for the seamless payments.

## Notification Handler

The notification handler will accept notification server requests,
and provide the status, amounts, payment methods actually used,
transactionReference.

```php
$request = $gateway->acceptNotification();

// $request->getTransactionId();
// $request->getTransactionReference();
// $request->getTransactionStatus();
// $request->getMoney();
// $request->isSuccessful();
// $request->getData();

```

