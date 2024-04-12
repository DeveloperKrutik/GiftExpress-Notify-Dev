<?php
    $access_token = "shpua_5a8b7553fa02ff4be471f615e19d2788";
    $shopname = "krutik-patel";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://'.$shopname.'.myshopify.com/admin/api/2023-10/webhooks/'.$_GET['wh'].'.json');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');


    $headers = array();
    $headers[] = 'X-Shopify-Access-Token:'.$access_token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    print_r($result);
    // header("Location: index.php");
    die();
?>