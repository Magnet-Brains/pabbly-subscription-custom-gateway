<?php

require('vendor/autoload.php');
require __DIR__ . '/lib/Subscription.php';

if (!isset($_GET['hostedpage'])) {
    throw new Exception('Hosted page data is required');
}
//Api credential of Pabbly Subscription
$apiKey = ""; //Put your api key here
$apiSecret = ""; // Put your api secret here

$hostedpage = $_GET['hostedpage'];
$subscription = new Subscription($apiKey, $apiSecret);
//Get hosted page details
try {
    $api_data = $subscription->hostedPage($hostedpage);
} catch (Exception $e) {
    die($e->getMessage());
}

$_subscription = $api_data->subscription;

if (!property_exists($api_data, "invoice")) {
	//If the subscription is trial, activate it and redirect to thank you page
	if ($_subscription->trial_days > 0) {
		//Do here your additional things if require.
		//Activate the trial subscription
		$subscription->activateTrialSubscription($_subscription->id);
		//Redirect to the thank you page
		$subscription->redirectThankYou($api_data->subscription->id, $api_data->subscription->customer_id, $api_data->product->redirect_url);
	}
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
    $payment_note = "Custom gateway"; //Note for your payment transaction if any
    $api_data = $subscription->recordPayment($invoice_id, $payment_mode, $payment_note, $transaction_data);

//Redirct to thank you page
    $subscription->redirectThankYou($api_data->subscription->id, $api_data->subscription->customer_id, $api_data->product->redirect_url);
} catch (Exception $e) {
    die($e->getMessage());
}
