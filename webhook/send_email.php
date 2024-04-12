<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  include_once('../config/common.php');
  require '../vendor/autoload.php';

  define('CLIENT_SECRET', '20202e5af20323aa184eaa7d22dc8055');
  function verify_webhook($data, $hmac_header)
  {
    $calculated_hmac = base64_encode(hash_hmac('sha256', $data, CLIENT_SECRET, true));
    return hash_equals($calculated_hmac, $hmac_header);
  }

  function sendMail($host, $username, $password, $encryption, $port, $to, $subject, $template, $name){
    $mail = new PHPMailer(true);
    $myfile = fopen("email_logs.txt", "a");
      
    try {
        $mail->SMTPDebug = 2;                                      
        $mail->isSMTP();                                           
        
        $mail->Host       = $host;                   
        $mail->SMTPAuth   = true;                            
        $mail->Username   = $username;                
        $mail->Password   = $password;                       
        $mail->SMTPSecure = $encryption;                             
        $mail->Port       = $port; 
        $mail->setFrom($username, $name);

        $mail->addAddress($to, $subject);
          
        $mail->isHTML(true);                                 
        $mail->Subject = $subject;
        $mail->Body    = $template;
        $mail->send();

        fwrite($myfile, "------------------------------------------------------\n");
        fwrite($myfile, "SUCCESS\n");
        fwrite($myfile, date("Y-m-d h:i:sa")."\n");
        fwrite($myfile, "Email sent....\n");
        fwrite($myfile, $to."\n");
        fwrite($myfile, "------------------------------------------------------\n");
        fwrite($myfile, "\n");
        fclose($myfile);
        
        return true;
        
    } catch (Exception $e) {
      fwrite($myfile, "------------------------------------------------------\n");
      fwrite($myfile, "ERROR\n");
      fwrite($myfile, date("Y-m-d h:i:sa")."\n");
      fwrite($myfile, $mail->ErrorInfo."\n");
      fwrite($myfile, "------------------------------------------------------\n");
      fwrite($myfile, "\n");
      fclose($myfile);

      return false;
    }
  }

  $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
  $shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
  $response = file_get_contents('php://input');
  $verified = verify_webhook($response, $hmac_header);
  
  $response_arr = json_decode($response);
  $lineitem_data = $response_arr->line_items;
  // $shop = explode('/', ltrim($response_arr->order_status_url, "https://"))[0];
  
  $properties = $lineitem_data[0]->properties;

  $getuserquery = "SELECT id FROM users WHERE shop = '".$shop."' and disflag = '0' ";
  $getuser = $obj->select($getuserquery);

  $userid = $getuser[0]['id'];

  $getsmtpquery = "SELECT * FROM smtp_config WHERE user_id = '".$userid."' and verified = '1' ";
  $getsmtp = $obj->select($getsmtpquery);

  $gettemplatequery = "SELECT * FROM email_template WHERE user_id = '".$userid."' ";
  $gettemplate = $obj->select($gettemplatequery);

  // $myfile = fopen("email_logs.txt", "w");
  // fwrite($myfile, json_encode($gettemplate));
  // fclose($myfile);
  // die();

  if (($verified == 1) && (count($gettemplate) > 0)) {
    $subject = $gettemplate[0]['subject'];
    $template = $gettemplate[0]['template'];
    $tomail = '0';
    foreach ($properties as $prop) {
      $subject = str_replace("{{".$prop->name."}}",$prop->value,$subject);
      $template = str_replace("{{".$prop->name."}}",$prop->value,$template);

      if($prop->name == $gettemplate[0]['to_email']){
        $tomail = $prop->value;
      }
    } 

    if($tomail != 0){
      if($gettemplate[0]['from_email'] == $getsmtp[0]['username']){
        if(sendMail($getsmtp[0]['host'], $getsmtp[0]['username'], $getsmtp[0]['password'], $getsmtp[0]['encryption'], $getsmtp[0]['port'], $tomail, $subject, $template, $getsmtp[0]['name'])){
          $userday = date("l");

          $insertmailquery = "INSERT INTO emails (user_id, userday) VALUES ('".$userid."', '".$userday."') ";
          $insertmail = $obj->insert($insertmailquery);
        }
      }else{
        if(sendMail('smtp.gmail.com', 'er.krutikpatel31@gmail.com', 'rjry kgvt qvzz stze', 'ssl', 465, $tomail, $subject, $template, 'GiftExpress Notify')){
          $userday = date("l");

          $insertmailquery = "INSERT INTO emails (user_id, userday) VALUES ('".$userid."', '".$userday."') ";
          $insertmail = $obj->insert($insertmailquery);
        }
      }
    }
  }else{
    $myfile = fopen("email_logs.txt", "a");
    fwrite($myfile, "------------------------------------------------------\n");
    fwrite($myfile, "ERROR\n");
    fwrite($myfile, date("Y-m-d h:i:sa")."\n");
    fwrite($myfile, "Does not verfied...\n");
    fwrite($myfile, "------------------------------------------------------\n");
    fwrite($myfile, "\n");
    fclose($myfile);
  }
?>