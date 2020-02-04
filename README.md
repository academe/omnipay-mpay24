
## Seamless

Create token.

```php
use Omnipay\Mpay24\Redirect;
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
    //'templateSet' => 'buzz',
]);

$response = $request->send();

if (! $response->isSuccessful()) {
    // Token could not be generated.
    echo '<p>Error: '.$response->getReturnCode().'</p>';
    exit;
}
```

This gives us:

```php
$response->getRedirectUrl();
$response->getToken()
```

The payment form is created like this, with `/pay` as the next stage.
The iframe will contain the rendered credit card form.
Add whatever additional customer details you want to the form.
(The token possibly does not need to do through the form, but could be
carried forward through the session instead.)

```php
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

```php
use Omnipay\Mpay24\Redirect;
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
to be needed to complete 3D Secure actions:

```php
if (! $response->isSuccessful() && $response->isRedirect()) {
    $response->redirect();
    exit;
}
```

After 3D Secure is completed, you will be returned to the `/complete` endpoint
where you need to fetch the results of the transation:

```
use Omnipay\Mpay24\Redirect;
use Omnipay\Omnipay;

$gateway = Omnipay::create('Mpay24_Seamless');

$gateway->setMerchantId('12345');
$gateway->setPassword('AB1234cd56');
$gateway->setTestMode(true);

$request = $gateway->completePurchase([
    // Will be in $_GET['TID'], but don't trust that.
    'transactionId' => $transactionId,
]);

$response = $request->send();
```

