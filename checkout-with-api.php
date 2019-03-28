<?php

//Subscription plan id
$plan_id = "Put your Pabbly Subscription plan id here";

//Api credential of Pabbly Subscription
$apiKey = ""; //Put your api key here
$apiSecret = ""; // Put your api secret here
$subscription = new Subscription($apiKey, $apiSecret);
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
    $api_data = $subscription->recordPayment($invoice_id, $payment_mode, $payment_note, $transaction_data);

    //Redirct to thank you page, you can also redirect or show your custom thank you page
    $subscription->redirectThankYou($api_data->subscription->id, $api_data->subscription->customer_id);
} catch (Exception $e) {
    die($e->getMessage());
}
 