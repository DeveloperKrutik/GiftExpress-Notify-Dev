<?php
  include_once('../../inc/functions.php');
  include_once('../../config/common.php');
  include_once('../../inc/variable.php');

  $params = $_GET;
  $hmac = $params['hmac'];
  $shopname = $params['shop'];

  $params = array_diff_key($params, array('hmac' => ''));
  if(isset($_GET['removeconfig'])){
    $params = array_diff_key($params, array('removeconfig' => ''));
  }
  ksort($params);
  $calculated_hmac = hash_hmac('sha256', http_build_query($params), $secret);

  if($hmac == $calculated_hmac){

    $requestparams = array("host"=> $params['host'], "shop"=> $params['shop']);
    ksort($requestparams);
    $index_hmac = hash_hmac('sha256', http_build_query($requestparams), $secret);
    $requestparams['hmac'] = $index_hmac;
    
    $getuser = "SELECT * FROM users WHERE shop = '".$shopname."' AND disflag = '0' ";
		$user = $obj->select($getuser);

		if((count($user) > 0) && ($user[0]['token'] != "")){

      $getsmtpquery = "SELECT * FROM smtp_config WHERE user_id = '".$user[0]['id']."' ";
      $getsmtp = $obj->select($getsmtpquery);

      if(isset($_GET['removeconfig'])){
        $removetemplatequery = "DELETE FROM email_template WHERE from_email = '".$getsmtp[0]['username']."' ";
        $removetemplate = $obj->delete($removetemplatequery);
        $removesmtpquery = "DELETE FROM smtp_config WHERE user_id = '".$user[0]['id']."' ";
        $removesmtp = $obj->delete($removesmtpquery);
        $hasData = 0;
        $smtp_host = "smtp.gmail.com";
        $port = "465";
        $encryption = "ssl";
        $username = "";
        $password = "";
      }else if(count($getsmtp) > 0){
        $hasData = 1;
        $smtp_host = $getsmtp[0]['host'];
        $port = $getsmtp[0]['port'];
        $encryption = $getsmtp[0]['encryption'];
        $username = $getsmtp[0]['username'];
        $password = $getsmtp[0]['password'];
      }else{
        $hasData = 0;
        $smtp_host = "smtp.gmail.com";
        $port = "465";
        $encryption = "ssl";
        $username = "";
        $password = "";
      }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Default Structure</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../../assets/css/flowbite.css">
        <script src="../../assets/js/tailwind.js"></script>
        <script src="../../assets/js/flowbite.min.js"></script>
        <script src="../../assets/js/jquery.js"></script>
        <script src="../../assets/js/ajax.js"></script>
        <script src="../../assets/js/appbridge.js"></script>
    </head>
    <body>

        <div class="py-5 px-20">
            <div class="border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500">
                    <li class="me-2">
                        <a href="../../index.php?<?php echo http_build_query($requestparams); ?>" class="inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 group" aria-current="page">
                            <svg class="w-4 h-4 me-2 text-gray-400 group-hover:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 18">
                                <path d="M6.143 0H1.857A1.857 1.857 0 0 0 0 1.857v4.286C0 7.169.831 8 1.857 8h4.286A1.857 1.857 0 0 0 8 6.143V1.857A1.857 1.857 0 0 0 6.143 0Zm10 0h-4.286A1.857 1.857 0 0 0 10 1.857v4.286C10 7.169 10.831 8 11.857 8h4.286A1.857 1.857 0 0 0 18 6.143V1.857A1.857 1.857 0 0 0 16.143 0Zm-10 10H1.857A1.857 1.857 0 0 0 0 11.857v4.286C0 17.169.831 18 1.857 18h4.286A1.857 1.857 0 0 0 8 16.143v-4.286A1.857 1.857 0 0 0 6.143 10Zm10 0h-4.286A1.857 1.857 0 0 0 10 11.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 18 16.143v-4.286A1.857 1.857 0 0 0 16.143 10Z"/>
                            </svg>Dashboard
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="../smtp/index.php?<?php echo http_build_query($requestparams); ?>" class="inline-flex items-center justify-center p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active group">
                            <svg class="w-4 h-4 me-2 text-blue-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 11.424V1a1 1 0 1 0-2 0v10.424a3.228 3.228 0 0 0 0 6.152V19a1 1 0 1 0 2 0v-1.424a3.228 3.228 0 0 0 0-6.152ZM19.25 14.5A3.243 3.243 0 0 0 17 11.424V1a1 1 0 0 0-2 0v10.424a3.227 3.227 0 0 0 0 6.152V19a1 1 0 1 0 2 0v-1.424a3.243 3.243 0 0 0 2.25-3.076Zm-6-9A3.243 3.243 0 0 0 11 2.424V1a1 1 0 0 0-2 0v1.424a3.228 3.228 0 0 0 0 6.152V19a1 1 0 1 0 2 0V8.576A3.243 3.243 0 0 0 13.25 5.5Z"/>
                            </svg>SMTP
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="../email_template/index.php?<?php echo http_build_query($requestparams); ?>" class="inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 group">
                            <svg class="w-4 h-4 me-2 text-gray-400 group-hover:text-gray-500" viewBox="0 0 24 24" fill="currentColor"><defs><style>.cls-1{opacity:0;}.cls-2{}</style></defs><title>email</title><g id="Layer_2" data-name="Layer 2"><g id="email"><g id="email-2" data-name="email"><rect class="cls-1" width="24" height="24"/><path class="cls-2" d="M19,4H5A3,3,0,0,0,2,7V17a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V7A3,3,0,0,0,19,4Zm-.67,2L12,10.75,5.67,6ZM19,18H5a1,1,0,0,1-1-1V7.25l7.4,5.55a1,1,0,0,0,.6.2,1,1,0,0,0,.6-.2L20,7.25V17A1,1,0,0,1,19,18Z"/></g></g></g></svg>Email Template
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="../knowledge_base/index.php?<?php echo http_build_query($requestparams); ?>" class="inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 group">
                            <svg class="w-4 h-4 me-2 text-gray-400 group-hover:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z"/>
                            </svg>Knowledge base
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="../contact_us/index.php?<?php echo http_build_query($requestparams); ?>" class="inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 group">
                            <svg class="w-4 h-4 me-2 text-gray-400 group-hover:text-gray-500" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21 8V7l-3 2-3-2v1l3 2 3-2zm1-5H2C.9 3 0 3.9 0 5v14c0 1.1.9 2 2 2h20c1.1 0 1.99-.9 1.99-2L24 5c0-1.1-.9-2-2-2zM8 6c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm6 12H2v-1c0-2 4-3.1 6-3.1s6 1.1 6 3.1v1zm8-6h-8V6h8v6z"/></svg>Contact us
                        </a>
                    </li>
                </ul>
            </div>

          <?php if($hasData == 0){ ?>
            <div class="grid grid-cols-2 px-10 py-5 mx-10 my-5 gap-4 bg-white rounded-md border border-gray-100 shadow">
              <p class="font-medium col-span-2 border-b border-gray-200">
                Create new STMP Configurations
                <br>
                <?php if(isset($err)){ ?><span class="font-medium text-red-500 text-xs">**<?php echo $err; ?></span><?php } ?>
              </p>

              <div>
                  <label for="smtp_host" class="block mb-1 text-gray-900">STMP Host:</label>
                  <input type="text" class="bg-gray-50 duration-200 border border-gray-300 text-gray-900 rounded-sm focus:ring-black focus:border-black focus:drop-shadow-md focus:shadow-black/50 block w-full px-2 py-1.5" id="smtp_host" placeholder="Enter SMTP host" name="smtp_host" value="<?php echo $smtp_host; ?>" required >
              </div>

              <div>
                  <label for="port" class="block mb-1 text-gray-900">Port:</label>
                  <input type="text" class="bg-gray-50 duration-200 border border-gray-300 text-gray-900 rounded-sm focus:ring-black focus:border-black focus:drop-shadow-md focus:shadow-black/50 block w-full px-2 py-1.5" id="port" placeholder="Enter port" name="port" value="<?php echo $port; ?>" required >
              </div>

              <div>
                  <label for="encryption" class="block mb-1 text-gray-900">Encryption method:</label>
                  <input type="text" class="bg-gray-50 duration-200 border border-gray-300 text-gray-900 rounded-sm focus:ring-black focus:border-black focus:drop-shadow-md focus:shadow-black/50 block w-full px-2 py-1.5" id="encryption" placeholder="Enter encryption method" name="encryption" value="<?php echo $encryption; ?>" required >
              </div>

              <div>
                  <label for="username" class="block mb-1 text-gray-900">Email address(Username):</label>
                  <input type="text" class="bg-gray-50 duration-200 border border-gray-300 text-gray-900 rounded-sm focus:ring-black focus:border-black focus:drop-shadow-md focus:shadow-black/50 block w-full px-2 py-1.5" id="username" placeholder="abc@gmail.com" name="username" value="<?php echo $username; ?>" required >
              </div>

              <div>
                  <label for="password" class="block mb-1 text-gray-900">Password:</label>
                  <input type="password" class="bg-gray-50 duration-200 border border-gray-300 text-gray-900 rounded-sm focus:ring-black focus:border-black focus:drop-shadow-md focus:shadow-black/50 block w-full px-2 py-1.5" id="password" placeholder="Enter password" name="password" value="<?php echo $password; ?>" required >
              </div>

              <div class="col-span-2">
                <button type="submit" name="create_smtp" class="bg-gray-800 text-sm font-medium px-5 py-1.5 text-white rounded shadow-lg hover:bg-gray-900 hover:shadow-none duration-150" onclick="whitelistSMTP();">
                  Create
                </button>
              </div>
            </div>

            <?php
              $whitelist_params = array("user"=> $user[0]['id'], "shopname"=> $shopname);
              $whitelist_hmac = hash_hmac('sha256', http_build_query($whitelist_params), $secret);
            ?>
            
            <script>
              function whitelistSMTP(){

                var smtp_host = $("#smtp_host").val();
                var port = $("#port").val();
                var encryption = $("#encryption").val();
                var username = $("#username").val();
                var password = $("#password").val();
                
                $.ajax({
                  type: 'post',
                  url: 'ajax/whitelistSMTP.php',
                  data: {
                    smtp_host : smtp_host,
                    port : port,
                    encryption : encryption,
                    username : username,
                    password : password,
                    user : <?php echo $user[0]['id']; ?>,
                    shopname : '<?php echo $shopname; ?>',
                    hmac : '<?php echo $whitelist_hmac; ?>'
                  },
                  success: function(data){
                    const obj = JSON.parse(data);
                    var AppBridge = window['app-bridge'];
                    var actions = AppBridge.actions;
                    
                    const config = {
                      apiKey: "<?php echo $apikey; ?>",
                      host: "<?php echo $_GET['host']; ?>",
                      forceRedirect: true
                    };
                    const app = AppBridge.createApp(config);
                    var Toast = actions.Toast;
                    if (obj.status == 1){
                      var toastOptions = {
                        message: obj.msg,
                        duration: 5000,
                      };
                      var toast = Toast.create(app, toastOptions); 
                      toast.dispatch(Toast.Action.SHOW);
                      window.location.href = "../smtp/index.php?<?php echo http_build_query($requestparams); ?>";
                    }else{
                      var toastOptions = {
                        message: obj.msg,
                        duration: 5000,
                        isError: true,
                      };
                      var toast = Toast.create(app, toastOptions); 
                      toast.dispatch(Toast.Action.SHOW);
                    }
                  }
                });
              }
            </script>
          
          <?php }else{ ?>
            <div class="px-10 py-5 mx-10 my-5 gap-4 bg-white rounded-md border border-gray-100 shadow">
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 text-center">
                    <thead class="text-xs text-white uppercase bg-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Verify
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Username
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Host
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Port
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Encryption
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                      <tr class="bg-white border-b">
                        <?php if($getsmtp[0]['verified'] == '0'){ ?>

                          <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            <div class="flex items-center justify-center">
                              <span class="cursor-pointer font-medium text-cyan-500 flex items-center w-fit hover:text-cyan-600 duration-150" title="Click to verify!" data-modal-target="verify-modal" data-modal-toggle="verify-modal">
                                <svg version="1.1" id="circle-stroked" width="18" height="18" viewBox="0 0 15 15" fill="currentColor"> <path id="path8564-5-6-4" d="M7.5,0C11.6422,0,15,3.3578,15,7.5S11.6422,15,7.5,15&#xA;&#x9;S0,11.6422,0,7.5S3.3578,0,7.5,0z M7.5,1.6666c-3.2217,0-5.8333,2.6117-5.8333,5.8334S4.2783,13.3334,7.5,13.3334&#xA;&#x9;s5.8333-2.6117,5.8333-5.8334S10.7217,1.6666,7.5,1.6666z"/> </svg>
                              </span>
                            </div>
                          </th>

                          <div id="verify-modal" tabindex="-1" aria-hidden="true" class="fixed bg-gray-50/25 top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                            <div class="relative w-full max-w-xl max-h-full">
                                <!-- Modal content -->
                                <div class="relative bg-white rounded-lg shadow">
                                    <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-hide="verify-modal">
                                      <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>  
                                      <span class="sr-only">Close modal</span>
                                    </button>
                                    <!-- Modal header -->
                                    <div class="px-6 py-4 border-b rounded-t">
                                      <h3 class="text-base font-semibold text-gray-900 lg:text-xl">
                                        Verify SMTP Configuration
                                      </h3>
                                    </div>
                                    <!-- Modal body -->
                                    <div class="px-6 pt-3">
                                      <label for="testemail" class="block mb-2 text-sm font-medium text-gray-900">Enter your email</label>
                                      <input type="email" id="testemail" name="testemail" class="bg-white duration-200 border border-gray-300 text-gray-900 rounded-sm focus:ring-emerald-500 focus:border-emerald-500 focus:drop-shadow-md focus:shadow-emerald-500/50 block w-full p-2" required placeholder="to:">
                                      <span class="font-medium text-gray-500 text-xs">Enter your email address for sending a test email from this configurations.</span>
                                    </div>

                                    <div class="px-6 py-3 flex justify-end">
                                      <button type="button" class="focus:outline-none text-black border border-gray-400 bg-white hover:bg-gray-200 focus:ring-4 focus:ring-gray-300 font-medium rounded text-sm px-4 py-1.5" data-modal-hide="verify-modal">Cancel</button>

                                      <button class="focus:outline-none text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-300 font-medium rounded text-sm px-4 py-1.5 ml-3" onclick="verifySMTPConfig(<?php echo $user[0]['id'] ?>, <?php echo $getsmtp[0]['id'] ?>);">Verify</button>
                                    </div>
                                </div>
                            </div>
                          </div>

                        <?php }else{ ?>
                          <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            <div class="flex items-center justify-center">
                              <span class="cursor-pointer font-medium text-cyan-500 flex items-center w-fit hover:text-cyan-600 duration-150" title="Verified!">
                                <svg width="18" height="18" viewBox="0 0 15 15" fill="currentColor"> <path fill-rule="evenodd" clip-rule="evenodd" d="M0 7.5C0 3.35786 3.35786 0 7.5 0C11.6421 0 15 3.35786 15 7.5C15 11.6421 11.6421 15 7.5 15C3.35786 15 0 11.6421 0 7.5ZM7.0718 10.7106L11.3905 5.31232L10.6096 4.68762L6.92825 9.2893L4.32012 7.11586L3.67993 7.88408L7.0718 10.7106Z" fill="currentColor"/> </svg>
                              </span>
                            </div>
                          </th>
                        <?php } ?>
                          <td class="px-6 py-4">
                            <?php echo $username; ?>
                          </td>
                          <td class="px-6 py-4">
                            <?php echo $smtp_host; ?>
                          </td>
                          <td class="px-6 py-4">
                            <?php echo $port; ?>
                          </td>
                          <td class="px-6 py-4">
                            <?php echo $encryption; ?>
                          </td>
                          <td class="px-6 py-4">
                            <div class="flex items-center justify-center">
                              <a href="../smtp/index.php?<?php echo http_build_query($requestparams); ?>&removeconfig=1" id="remove-smtp" class="cursor-pointer font-medium text-red-500 flex items-center w-fit hover:text-red-600 duration-150" title="Remove config">
                                <svg width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"> <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/> <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/> </svg>
                              </a>
                            </div>
                          </td>
                      </tr>
                    </tbody>
                </table>
              </div> 
            </div>

            <?php
              $verify_params = array("user"=> $user[0]['id'], "smtp"=> $getsmtp[0]['id']);
              $verify_hmac = hash_hmac('sha256', http_build_query($verify_params), $secret);  
            ?>

            <script>
              function verifySMTPConfig(user, smtp){
                var email = $("#testemail").val();
                $.ajax({
                  type: 'post',
                  url: 'ajax/verifySMTPConfig.php',
                  data: {
                    email : email,
                    user : user,
                    smtp : smtp,
                    hmac : '<?php echo $verify_hmac; ?>'
                  },
                  success: function(data){
                    const obj = JSON.parse(data);
                    var AppBridge = window['app-bridge'];
                    var actions = AppBridge.actions;
                    
                    const config = {
                      apiKey: "<?php echo $apikey; ?>",
                      host: "<?php echo $_GET['host']; ?>",
                      forceRedirect: true
                    };
                    const app = AppBridge.createApp(config);
                    var Toast = actions.Toast;
                    if (obj.status == 1){
                      var toastOptions = {
                        message: obj.msg,
                        duration: 5000,
                      };
                      var toast = Toast.create(app, toastOptions); 
                      toast.dispatch(Toast.Action.SHOW);
                      window.location.href = "../smtp/index.php?<?php echo http_build_query($requestparams); ?>";
                    }else{
                      var toastOptions = {
                        message: obj.msg,
                        duration: 5000,
                        isError: true,
                      };
                      var toast = Toast.create(app, toastOptions); 
                      toast.dispatch(Toast.Action.SHOW);
                    }
                  }
                });
              }
            </script>

          <?php } ?>

        </div>
        
    </body>
</html>
<?php
        }else{
          $install_url = "../../install.php?shop=".$shopname."";
          header("Location: " . $install_url);
          die();
        }
    }else{
        echo "Something went wrong, please try again!";
    }
?>