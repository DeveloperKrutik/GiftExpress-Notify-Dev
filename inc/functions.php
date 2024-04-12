<?php

	function send_email_webhook($user, $access_token, $shopname) {

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://'.$shopname.'/admin/api/2023-10/webhooks.json');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

		$headers = array();
		$headers[] = 'X-Shopify-Access-Token:'.$access_token;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$webhook = curl_exec($ch);
		if (curl_errno($ch)) {
				echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);
		$webhook = json_decode($webhook);
		
		$tmp = false;
		for($i=0; $i<count($webhook->webhooks); $i++){
			if($webhook->webhooks[$i]->topic == 'orders/create'){
				$tmp = $i;
			}
		}

		if($tmp == false){

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, 'https://'.$shopname.'/admin/api/2023-10/webhooks.json');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"webhook\":{\"topic\":\"orders/create\",\"address\":\"https://e60d-103-54-105-90.ngrok-free.app/shopify-app/GiftNotificationDev/webhook/send_email.php/\",\"format\":\"json\"}}");

			$headers = array();
			$headers[] = 'X-Shopify-Access-Token:'.$access_token;
			$headers[] = 'Content-Type: application/json';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$result = curl_exec($ch);
			if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
			}
			curl_close($ch);
			$result = json_decode($result);
			
			return $result->webhook->id;

		}else{		
			return $webhook->webhooks[$tmp]->id;
		}
			
	}

	function uninstall_app_webhook($access_token, $shopname) {

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://'.$shopname.'/admin/api/2023-10/webhooks.json');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"webhook\":{\"topic\":\"app/uninstalled\",\"address\":\"https://e60d-103-54-105-90.ngrok-free.app/shopify-app/GiftNotificationDev/webhook/uninstall_app.php/\",\"format\":\"json\"}}");

		$headers = array();
		$headers[] = 'X-Shopify-Access-Token:'.$access_token;
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
				echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);
		$result = json_decode($result);
		
		return $result->webhook->id;
			
	}

?>