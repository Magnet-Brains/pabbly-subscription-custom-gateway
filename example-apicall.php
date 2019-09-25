<?php
require('vendor/autoload.php');
require __DIR__ . '/lib/Subscription.php';

$apiKey = ""; //Put your api key here
$apiSecret = ""; // Put your api secret here

$subscription = new Subscription($apiKey, $apiSecret);

//Retrieve customer detail example
$customer_id = "";
$api_data = $subscription->getCustomer($customer_id);

//View details
print_r($api_data);