<?php
  include_once('../../../config/common.php');
  include_once('../../../inc/variable.php');

  $status = 0;
  $msg = "";
  
  if((isset($_POST['smtp_host'])) && (isset($_POST['port'])) && (isset($_POST['shopname'])) && (isset($_POST['encryption'])) && (isset($_POST['username'])) && (isset($_POST['password'])) && (isset($_POST['user'])) && (isset($_POST['hmac']))){

      $smtp_host = str_replace("'","&qout;",trim($_POST['smtp_host'], " "));
      $shopname = str_replace("'","&qout;",trim($_POST['shopname'], " "));
      $port = str_replace("'","&qout;",trim($_POST['port'], " "));
      $encryption = str_replace("'","&qout;",trim($_POST['encryption'], " "));
      $username = str_replace("'","&qout;",trim($_POST['username'], " "));
      $password = str_replace("'","&qout;",trim($_POST['password'], " "));
      $user = trim($_POST['user'], " ");
      $hmac = trim($_POST['hmac'], " ");

      $getuser = "SELECT token FROM users WHERE id = '".$user."' AND disflag = '0' ";
		  $userdata = $obj->select($getuser);

      $params = array("user"=> $user, "shopname"=> $shopname);
      $calculated_hmac = hash_hmac('sha256', http_build_query($params), $secret);

      if(($hmac == $calculated_hmac) && (count($userdata) > 0)){

        $invalidemail = 0;
        $usernameexist = 0;

        $checksmtpquery = "SELECT id FROM smtp_config WHERE username = '".$username."' ";
        $checksmtp = $obj->select($checksmtpquery);
        if (count($checksmtp) > 0) {
          $usernameexist = 1;
        }
        
        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
          $invalidemail = 1;
        }

        if($smtp_host == ""){
          $msg = "Please enter SMTP host!";
        }else if($port == ""){
          $msg = "Please enter port number!";
        }else if($encryption == ""){
          $msg = "Please enter encryption method!";
        }else if($username == ""){
          $msg = "Please enter username!";
        }else if($password == ""){
          $msg = "Please enter password!";
        }else if($invalidemail == 1){
          $msg = "Invalid username!";
        }else if($usernameexist == 1){
          $msg = "Please use another username!";
        }else{

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, 'https://'.$shopname.'/admin/api/2024-01/shop.json');
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

          $headers = array();
          $headers[] = "X-Shopify-Access-Token: ".$userdata[0]['token'];
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

          $result = curl_exec($ch);
          if (curl_errno($ch)) {
              echo 'Error:' . curl_error($ch);
          }
          curl_close($ch);

          $shopname = json_decode($result)->shop->name;

          $insertsmtpquery = "INSERT INTO smtp_config(user_id, host, port, encryption, username, password, name) VALUES ('".$user."', '".$smtp_host."', '".$port."', '".$encryption."', '".$username."', '".$password."', '".$shopname."')";
          $insertsmtp = $obj->insert($insertsmtpquery);

          if($insertsmtp > 0){
            $status = '1';
            $msg = "SMTP configurations saved.";
          }
        }

      }else{
        $msg = "Something went wrong!";
      }
  }else{
    $msg = "Something went wrong!";
  }

  $json_data = array(
    "status"            => $status,   
    "msg"            => $msg
  );
  
  echo json_encode($json_data); 
?>