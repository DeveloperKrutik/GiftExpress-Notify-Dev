<?php
  include_once('../../../config/common.php');
  include_once('../../../inc/variable.php');

  $status = 0;
  $msg = "";
  
  ob_start();
  
  if((isset($_POST['fromemail'])) && (isset($_POST['toemail'])) && (isset($_POST['subject'])) && (isset($_POST['template'])) && (isset($_POST['user'])) && (isset($_POST['hmac']))){

    $fromemail = trim($_POST['fromemail'], " ");
    $toemail = trim($_POST['toemail'], " ");
    $subject = trim($_POST['subject'], " ");
    $template = trim($_POST['template'], " ");
    $user = trim($_POST['user'], " ");
    $hmac = trim($_POST['hmac'], " ");

    $params = array("user"=> $user);
    $calculated_hmac = hash_hmac('sha256', http_build_query($params), $secret);

    if($hmac == $calculated_hmac){
      
      if($toemail == ""){
        $msg = "'Send email to' field is required!";
      }else{

        if($fromemail != '0'){
          $getsmtpquery = "SELECT username FROM smtp_config WHERE user_id = '".$user."' AND verified = 1 ";
          $getsmtp = $obj->select($getsmtpquery);
          if($fromemail == $getsmtp[0]['username']){
            $fromemail = $getsmtp[0]['username'];
          }else{
            $fromemail = '0';
          }
        }

        $gettemplatequery = "SELECT id FROM email_template WHERE user_id = '".$user."' ";
        $gettemplate = $obj->select($gettemplatequery);

        if(count($gettemplate) > 0){
          $updatetemplatequery = "UPDATE email_template SET from_email = '".$fromemail."', to_email = '".$toemail."', subject = '".$subject."', template = '".$template."' WHERE user_id = '".$user."' ";
          $update = $obj->edit($updatetemplatequery);
        }else{
          $inserttemplatequery = "INSERT INTO email_template(user_id, from_email, to_email, subject, template) VALUES ('".$user."', '".$fromemail."', '".$toemail."', '".$subject."', '".$template."')";
          $insert = $obj->insert($inserttemplatequery);
        }
        $status = 1;
        $msg = "Email template saved.";
      }
    }else{
      $msg = "Something went wrong!";
    }
  }else{
    $msg = "Something went wrong!";
  }
  
  ob_end_clean();

  $json_data = array(
    "status"            => $status,   
    "msg"            => $msg
  );
  
  echo json_encode($json_data); 
?>