<?php
  include_once('../../../config/common.php');
  include_once('../../../inc/variable.php');

  $status = 0;
  $msg = "";
  
  if((isset($_POST['name'])) && (isset($_POST['email'])) && (isset($_POST['message'])) && (isset($_POST['user'])) && (isset($_POST['hmac']))){

      $name = str_replace("'","&qout;",trim($_POST['name'], " "));
      $email = str_replace("'","&qout;",trim($_POST['email'], " "));
      $message = str_replace("'","&qout;",trim($_POST['message'], " "));
      $user = trim($_POST['user'], " ");
      $hmac = trim($_POST['hmac'], " ");

      $params = array("user"=> $user);
      $calculated_hmac = hash_hmac('sha256', http_build_query($params), $secret);

      if($hmac == $calculated_hmac){

        $invalidemail = 0;
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $invalidemail = 1;
        }

        if($name == ""){
          $msg = "Please enter your name!";
        }else if($email == ""){
          $msg = "Please enter your email address!";
        }else if($message == ""){
          $msg = "Please enter message!";
        }else if($invalidemail == 1){
          $msg = "Invalid username!";
        }else if(strlen($message) > 500){
          $msg = "Maximum 500 characters allowed in message!";
        }else{
          $insertcontactusquery = "INSERT INTO contact_us(user_id, name, email, message) VALUES ('".$user."', '".$name."', '".$email."', '".$message."')";
          $insertcontactus = $obj->insert($insertcontactusquery);

          if($insertcontactus > 0){
            $status = '1';
            $msg = "We'll get back to you soon.";
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