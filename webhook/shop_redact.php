<?php
  include_once('../config/common.php');
  define('CLIENT_SECRET', '20202e5af20323aa184eaa7d22dc8055');
  
  function verify_webhook($data, $hmac_header){
    $calculated_hmac = base64_encode(hash_hmac('sha256', $data, CLIENT_SECRET, true));
    return hash_equals($calculated_hmac, $hmac_header);
  }

  $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
  $data = file_get_contents('php://input');
  $verified = verify_webhook($data, $hmac_header);
  error_log('Webhook verified: '.var_export($verified, true));

  if ($verified) {

    $response_arr = json_decode($data);
    $shop = $response_arr->shop_domain;

    $deletewebhook = "DELETE FROM users WHERE shop = '".$shop."' ";
    $obj->delete($deletewebhook);
    http_response_code(200);

  } else {
    http_response_code(401);
  }
?>