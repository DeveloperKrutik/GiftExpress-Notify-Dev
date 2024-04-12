<?php

// Set variables for our request
$shop = $_GET['shop'];
$api_key = "f7d7125c34721c63dd02b64c738e4ed4";
$scopes = "read_orders, write_orders";
$redirect_uri = "https://e60d-103-54-105-90.ngrok-free.app/shopify-app/GiftNotificationDev/generate_token.php";

// Build install/approval URL to redirect to
$install_url = "https://" . $shop . ".myshopify.com/admin/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri);

// Redirect
header("Location: " . $install_url);
die();