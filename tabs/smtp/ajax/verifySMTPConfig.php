<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  include_once('../../../config/common.php');
  include_once('../../../inc/variable.php');

  $status = 0;
  $msg = "";
  ob_start();

  if((isset($_POST['email'])) && (isset($_POST['user'])) && (isset($_POST['smtp'])) && (isset($_POST['hmac']))){
    $email = trim($_POST['email'], " ");
    $user = trim($_POST['user'], " ");
    $smtp = trim($_POST['smtp'], " ");
    $hmac = trim($_POST['hmac'], " ");

    $params = array("user"=> $user, "smtp"=> $smtp);
    $calculated_hmac = hash_hmac('sha256', http_build_query($params), $secret);

    if($hmac == $calculated_hmac){

      if($email == ""){
        $msg = "Please enter an email!";
      }else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Invalid email format!";
      }else{
        $getsmtpquery = "SELECT * FROM smtp_config WHERE user_id = '".$user."' AND id = '".$smtp."' ";
        $getsmtp = $obj->select($getsmtpquery);

        if(count($getsmtp) > 0){
          
          require '../../../vendor/autoload.php';
          
          $mail = new PHPMailer(true);
          
          try {
              $mail->SMTPDebug = 2;                                      
              $mail->isSMTP();                                           
              $mail->Host       = $getsmtp[0]['host'];                   
              $mail->SMTPAuth   = true;                            
              $mail->Username   = $getsmtp[0]['username'];                
              $mail->Password   = $getsmtp[0]['password'];                       
              $mail->SMTPSecure = $getsmtp[0]['encryption'];                             
              $mail->Port       = $getsmtp[0]['port']; 
          
              $mail->setFrom($getsmtp[0]['username'], 'Test');          
              $mail->addAddress($email);
                
              $mail->isHTML(true);                                 
              $mail->Subject = 'SMTP Verification';
              $mail->Body    = 'Hello There,<br><br>
              This is a test mail from giftnotification shopify app.<br><br>
              <strong>Note:</strong> <span style="color: red;">Please do not reply to this mail. Email sent to this address will not be responded to.</span>';
              $mail->send();
              $status = 1;
              $obj->edit("UPDATE smtp_config SET verified = 1 WHERE user_id = '".$user."' AND id = '".$smtp."'");
              $msg = "SMTP Verified";
          } catch (Exception $e) {
            $msg = "{$mail->ErrorInfo}";
          }
          
        }else{
          $msg = "Something went wrong!";
        }
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