<?php

// Get our helper functions
require_once("inc/functions.php");
include_once('config/common.php');

// Set variables for our request
$api_key = "f7d7125c34721c63dd02b64c738e4ed4";
$shared_secret = "20202e5af20323aa184eaa7d22dc8055";
$params = $_GET; // Retrieve all request parameters
$hmac = $_GET['hmac']; // Retrieve HMAC request parameter

$params = array_diff_key($params, array('hmac' => '')); // Remove hmac from params
ksort($params); // Sort params lexographically

$computed_hmac = hash_hmac('sha256', http_build_query($params), $shared_secret);

// Use hmac data to check that the response is from Shopify or not
if (hash_equals($hmac, $computed_hmac)) {

	// Set variables for our request
	$query = array(
		"client_id" => $api_key, // Your API key
		"client_secret" => $shared_secret, // Your app credentials (secret key)
		"code" => $params['code'] // Grab the access key from the URL
	);

	// Generate access token URL
	$access_token_url = "https://" . $params['shop'] . "/admin/oauth/access_token";

	// Configure curl client and execute request
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $access_token_url);
	curl_setopt($ch, CURLOPT_POST, count($query));
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
	$result = curl_exec($ch);
	curl_close($ch);

	// Store the access token
	$result = json_decode($result, true);
	$access_token = $result['access_token'];

	// Show the access token (don't do this in production!)
	// echo $access_token;

	uninstall_app_webhook($access_token, $params['shop']);

	$checkshopquery = "SELECT id FROM users WHERE shop = '".$params['shop']."' ";
	$checkshop = $obj->select($checkshopquery);

	if (count($checkshop) > 0){
		$updatetokenquery = "UPDATE users SET token = '".$access_token."', disflag = 0 WHERE shop = '".$params['shop']."' ";
		$updatetoken = $obj->edit($updatetokenquery);

		if ($updatetoken){
			$wid = send_email_webhook($updatetoken, $access_token, $params['shop']);
			$updatewebhook = "UPDATE users SET webhook_id = '".$wid."' WHERE id = '".$updatetoken."' ";
			$obj->edit($updatewebhook);

			header("Location: https://". $params['shop'] . "/admin/apps/giftexpress-notify-dev");
			exit();
		}else{
			echo "Something went wrong! Please try again.";
		}
	}else{

		$inserttokenquery = "INSERT INTO users(shop, token, disflag) VALUES ('".$params['shop']."', '".$access_token."', 0)";
		$inserttoken = $obj->insert($inserttokenquery);

		if ($inserttoken){
			$wid = send_email_webhook($inserttoken, $access_token, $params['shop']);
			$updatewebhook = "UPDATE users SET webhook_id = '".$wid."' WHERE id = '".$inserttoken."' ";
			$obj->edit($updatewebhook);

			header("Location: https://". $params['shop'] . "/admin/apps/giftexpress-notify-dev");
			exit();
		}else{
			echo "Something went wrong! Please try again.";
		}
	}

} else {
	// Someone is trying to be shady!
	die('This request is NOT from Shopify!');
}