<?php
  
  $arr = $_SESSION['rdirectdurl'];
  $fields = explode("&",$arr);
  $shop = "";
  $host = "";

  foreach ($fields as $val) {
    $tmp = explode("=", $val);
    if($tmp[0] == 'shop'){
      $shop = $tmp[1];
    }else if($tmp[0] == 'host'){
      $host = $tmp[1];
    }
  }
?>