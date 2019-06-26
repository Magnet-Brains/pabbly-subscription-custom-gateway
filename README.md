
# Pabbly Subscription Custom Gateway Integration

Pabbly Subscription custom gateway integration for PHP.

## Installation

* If your project uses composer, install the [php-curl-class](https://github.com/php-curl-class/php-curl-class) composer package.
* You can also clone this repository and run:
	```code 
	composer install
	```
## Contents:

1. 	Integration through checkout page.
2. 	Integration through Pabbly Subscription [api](https://www.pabbly.com/subscriptions/api/#section2)

## Steps for Integration:

### 1. Integration through checkout page.
*	Add custom gateway in Pabbly Subscription payment integration.
*	Add your custom gateway host url in **Gateway Url** field.
* While submitting the checkout page, process will be redirected to your gateway url with hosted page details.
*  Then you need to call [verify hosted page api](https://www.pabbly.com/subscriptions/api/#section26)
* The api will return customer, product, plan and invoice details. You can use these details to process your custom gateway.
* You can also use following examples to perform this task:

### See the example below:

```php
<?php
require('vendor/autoload.php');
require  __DIR__  .  '/lib/Subscription.php';

if (!isset($_GET['hostedpage']) && !$_GET['hostedpage']) {
    throw new Exception('Hosted page data is required');
}
//Api credential of Pabbly Subscription
$apiKey = ""; //Put your api key here
$apiSecret = ""; // Put your api secret here

$hostedpage = $_GET['hostedpage'];
$subscription = new Subscription($apiKey,$apiSecret);
//Get hosted page details
try {
    $api_data = $subscription->hostedPage($hostedpage);
} catch (Exception $e) {
    die($e->getMessage());
}

$_subscription = $api_data->subscription;
//If the subscription is trial, activate it and redirect to thank you page
if ($_subscription->trial_days > 0) {
    //Do here your additional things if require.

    //Activate the trial subscription
    $api_data = $subscription->activateTrialSubscription($_subscription->id);

    //Redirect to the thank you page
    $subscription->redirectThanktyou($api_data->subscription->id, $api_data->subscription->customer_id);
}

$user = $api_data->user;
$customer = $api_data->customer;
$product = $api_data->product;
$plan = $api_data->plan;
$invoice = $api_data->invoice;
$currency = $user->currency;

//Do your payment processor task here

//After complete the payment process you have to record the payment for the invoice due. Use the following example for that:

try {
$invoice_id = $invoice->id;
$payment_mode = "Your custom gateway name";
$transaction_data = "Which returns by your custom gateway for your record"; //string/object
$api_data  =  $subscription->recordPayment($invoice_id, $payment_mode, $payment_note, $transaction_data);

//Redirct to thank you page
$subscription->redirectThankYou($api_data->subscription->id, $api_data->subscription->customer_id);
} catch (Exception  $e) {
die($e->getMessage());
}
?>
```

### 2. 	Integration through Pabbly Subscription [api](https://www.pabbly.com/subscriptions/api/#section2)

* Subscribe the plan through api.
* Use the following example to use by api:
```php
<?php
//Subscription plan id
$plan_id = "Put your Pabbly Subscription plan id here";

//Api credential of Pabbly Subscription
$apiKey = ""; //Put your api key here
$apiSecret = ""; // Put your api secret here
$subscription = new Subscription($apiKey,$apiSecret);
$api_data = array(
    'first_name' => $_POST['first_name'],
    'last_name' => $_POST['last_name'],
    'email' => $_POST['email'],
    'gateway_name' => 'razorpay',
    'street' => $_POST['street'],
    'city' => $_POST['city'],
    'state' => $_POST['state'],
    'zip_code' => $_POST['zip_code'],
    'country' => $_POST['country'],
    'plan_id' => $plan_id,
);

//Subscribe the plan
$apiResponse = $subscription->subscribe($api_data);

$_subscription = $apiResponse->subscription;
//If the subscription is trial, activate it and redirect to thank you page
if ($_subscription->trial_days > 0) {
    //Do here your additional things if require.

    //Activate the trial subscription
    $api_data = $subscription->activateTrialSubscription($_subscription->id);

    //Redirect to the thank you page
    $subscription->redirectThanktyou($api_data->subscription->id, $api_data->subscription->customer_id);
}

$user = $apiResponse->user;
$customer = $apiResponse->customer;
$product = $apiResponse->product;
$plan = $apiResponse->plan;
$invoice = $apiResponse->invoice;
$currency = $user->currency;

//Do your payment processor task here

//After complete the payment process you have to record the payment for the invoice due. Use the following example for that:

try {
$invoice_id = $invoice->id;
$payment_mode = "Your custom gateway name";
$transaction_data = "Which returns by your custom gateway for your record"; //string/object
$api_data  =  $subscription->recordPayment($invoice_id, $payment_mode, $payment_note, $transaction_data);

//Redirct to thank you page, you can also redirect or show your custom thank you page
$subscription->redirectThankYou($api_data->subscription->id, $api_data->subscription->customer_id);
} catch (Exception  $e) {
die($e->getMessage());
}
?>
```
