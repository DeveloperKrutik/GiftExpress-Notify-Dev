<?php
    include_once('../../inc/functions.php');
    include_once('../../inc/variable.php');
    include_once('../../config/common.php');

    $params = $_GET;
    $hmac = $params['hmac'];
    $shopname = $params['shop'];

    $params = array_diff_key($params, array('hmac' => ''));
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
  
            $getsmtpquery = "SELECT * FROM smtp_config WHERE user_id = '".$user[0]['id']."' AND verified = '1' ";
            $getsmtp = $obj->select($getsmtpquery);

            $gettemplatequery = "SELECT * FROM email_template WHERE user_id = '".$user[0]['id']."' ";
            $gettemplate = $obj->select($gettemplatequery);

            if(count($gettemplate) > 0){
                $from = $gettemplate[0]['from_email'];
                $to = $gettemplate[0]['to_email'];
                $subject = $gettemplate[0]['subject'];
                $template = $gettemplate[0]['template'];
            }else{
                $from = '';
                $to = '';
                $subject = 'Hey there, I have a gift for you...';
                $template = '
                    <h2>
                        Hello mate,
                    </h2>
                    <br>
                    <p>Happy birthday...</p>
                ';
            }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Email Template</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../../assets/css/flowbite.css">
        <script src="../../assets/js/tailwind.js"></script>
        <script src="../../assets/js/flowbite.min.js"></script>
        <script src="../../assets/js/jquery.js?v=<?php echo time(); ?>"></script>
        <script src="../../assets/js/ajax.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn3.devexpress.com/jslib/23.2.3/css/dx.material.blue.light.css" />
        <script src="https://unpkg.com/devextreme-quill@1.6.2/dist/dx-quill.min.js"></script>
        <script src="../../assets/js/dx.all.js"></script>
        <link rel="stylesheet" href="../../assets/css/wysiwyg.css">
        <script src="../../assets/js/appbridge.js"></script>
    </head>
    <body>

        <div class="py-5 px-20">
            <div class="border-b border-gray-200 mb-5">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500">
                    <li class="me-2">
                        <a href="../../index.php?<?php echo http_build_query($requestparams); ?>" class="inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 group" aria-current="page">
                            <svg class="w-4 h-4 me-2 text-gray-400 group hover:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 18">
                                <path d="M6.143 0H1.857A1.857 1.857 0 0 0 0 1.857v4.286C0 7.169.831 8 1.857 8h4.286A1.857 1.857 0 0 0 8 6.143V1.857A1.857 1.857 0 0 0 6.143 0Zm10 0h-4.286A1.857 1.857 0 0 0 10 1.857v4.286C10 7.169 10.831 8 11.857 8h4.286A1.857 1.857 0 0 0 18 6.143V1.857A1.857 1.857 0 0 0 16.143 0Zm-10 10H1.857A1.857 1.857 0 0 0 0 11.857v4.286C0 17.169.831 18 1.857 18h4.286A1.857 1.857 0 0 0 8 16.143v-4.286A1.857 1.857 0 0 0 6.143 10Zm10 0h-4.286A1.857 1.857 0 0 0 10 11.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 18 16.143v-4.286A1.857 1.857 0 0 0 16.143 10Z"/>
                            </svg>Dashboard
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="../smtp/index.php?<?php echo http_build_query($requestparams); ?>" class="inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 group">
                            <svg class="w-4 h-4 me-2 text-gray-400 group hover:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 11.424V1a1 1 0 1 0-2 0v10.424a3.228 3.228 0 0 0 0 6.152V19a1 1 0 1 0 2 0v-1.424a3.228 3.228 0 0 0 0-6.152ZM19.25 14.5A3.243 3.243 0 0 0 17 11.424V1a1 1 0 0 0-2 0v10.424a3.227 3.227 0 0 0 0 6.152V19a1 1 0 1 0 2 0v-1.424a3.243 3.243 0 0 0 2.25-3.076Zm-6-9A3.243 3.243 0 0 0 11 2.424V1a1 1 0 0 0-2 0v1.424a3.228 3.228 0 0 0 0 6.152V19a1 1 0 1 0 2 0V8.576A3.243 3.243 0 0 0 13.25 5.5Z"/>
                            </svg>SMTP
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="../email_template/index.php?<?php echo http_build_query($requestparams); ?>" class="inline-flex items-center justify-center p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active group">
                            <svg class="w-4 h-4 me-2 text-blue-600" viewBox="0 0 24 24" fill="currentColor"><defs><style>.cls-1{opacity:0;}.cls-2{}</style></defs><title>email</title><g id="Layer_2" data-name="Layer 2"><g id="email"><g id="email-2" data-name="email"><rect class="cls-1" width="24" height="24"/><path class="cls-2" d="M19,4H5A3,3,0,0,0,2,7V17a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V7A3,3,0,0,0,19,4Zm-.67,2L12,10.75,5.67,6ZM19,18H5a1,1,0,0,1-1-1V7.25l7.4,5.55a1,1,0,0,0,.6.2,1,1,0,0,0,.6-.2L20,7.25V17A1,1,0,0,1,19,18Z"/></g></g></g></svg>Email Template
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="../knowledge_base/index.php?<?php echo http_build_query($requestparams); ?>" class="inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 group">
                            <svg class="w-4 h-4 me-2 text-gray-400 group hover:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
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

            <div class="px-10 py-5 my-5 bg-white rounded-md border border-gray-100 shadow">

                <div class="grid grid-cols-2 gap-4 pb-2 border-b-2">
                    <div>
                        <label for="fromemail" class="block mb-1 text-sm font-medium text-gray-900">Send email from:</label>
                        <select id="fromemail" class="block w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-md bg-gray-50 focus:ring-black focus:border-black">
                            <option value="0" <?php if($from == '0' || $from == ''){ ?> selected <?php } ?> >Our default email</option>
                            <?php if(count($getsmtp) > 0){ ?>
                                <option value="<?php echo $getsmtp[0]['username']; ?>" <?php if($getsmtp[0]['username'] == $from){ ?> selected <?php } ?> ><?php echo $getsmtp[0]['username']; ?></option>
                            <?php } ?>
                        </select>
                        <?php if(count($getsmtp) == 0){ ?>
                            <span class="text-xs">You can whitelist <a href="../smtp/index.php" class="text-blue-800 font-medium cursor-pointer hover:underline">your own SMTP configurations</a> and send email from that email address.</span>
                        <?php } ?>
                    </div>

                    <div>
                        <label for="toemail" class="block mb-1 text-sm font-medium text-gray-900">Send email to:</label>
                        <input type="text" id="toemail" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-md bg-gray-50 text-sm focus:ring-black focus:border-black" value="<?php echo $to; ?>">
                        <span class="text-xs">Enter the input label of the giftnotification form to which you want to send an email. <span class="font-medium">This field is case sensetive.</span></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 pt-5">
                    <div class="text-xs col-span-2">
                        <span class="font-medium">NOTE:</span> To personalize the email subject and template, simply insert the input label between double curly braces, like this: {{Label}}. This dynamic approach allows for customized and adaptable content.
                    </div>
                    <div class="text-xs col-span-2 mb-4">
                        Example: If the label is 'Name' or 'Gift Message,' you can dynamically incorporate them by using {{Name}} and {{Gift Message}}, respectively.
                    </div>

                    <div class="col-span-2 dx-viewport demo-container">
                        <label for="subject" class="block mb-1 text-sm font-medium text-gray-900">Email subject:</label>
                        <input type="text" id="subject" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-md bg-gray-50 text-sm focus:ring-black focus:border-black" value="<?php echo $subject; ?>">
                    </div>
                    
                    <div class="my-4 col-span-2 dx-viewport demo-container">
                        <label for="template" class="block mb-1 text-sm font-medium text-gray-900">Email template:</label>
                        <div class="html-editor"></div>
                            
                        </div>
                    </div>

                    <div>
                        <button type="button" class="bg-gray-800 text-sm font-medium px-5 py-1.5 text-white rounded shadow-sm hover:bg-gray-900 hover:shadow-none duration-150" onclick="saveEMailTemplate('<?php echo $user[0]['id']; ?>');">
                            Save
                        </button>
                    </div>
                </div>
            </div>
            
        </div>
        <?php
          $save_params = array("user"=> $user[0]['id']);
          $save_hmac = hash_hmac('sha256', http_build_query($save_params), $secret);
        ?>
        <script>
            function saveEMailTemplate(user){
                var fromemail = $("#fromemail").val();
                var toemail = $("#toemail").val();
                var subject = $("#subject").val();
                var template = document.getElementsByClassName('ql-editor')[0].innerHTML;
                
                $.ajax({
                    type: 'post',
                    url: 'ajax/saveEMailTemplate.php',
                    data: {
                        fromemail : fromemail,
                        toemail : toemail,
                        subject : subject,
                        template : template,
                        user : user,
                        hmac : '<?php echo $save_hmac; ?>'
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

            $(() => {
                const editor = $('.html-editor').dxHtmlEditor({
                    height: 525,
                    value: `<?php echo $template; ?>`,
                    toolbar: {
                    items: [
                        'undo', 'redo', 'separator',
                        {
                        name: 'size',
                        acceptedValues: ['8pt', '10pt', '12pt', '14pt', '18pt', '24pt', '36pt'],
                        options: { inputAttr: { 'aria-label': 'Font size' } },
                        },
                        {
                        name: 'font',
                        acceptedValues: ['Arial', 'Courier New', 'Georgia', 'Impact', 'Lucida Console', 'Tahoma', 'Times New Roman', 'Verdana'],
                        options: { inputAttr: { 'aria-label': 'Font family' } },
                        },
                        'separator', 'bold', 'italic', 'strike', 'underline', 'separator',
                        'alignLeft', 'alignCenter', 'alignRight', 'alignJustify', 'separator',
                        'orderedList', 'bulletList', 'separator',
                        {
                        name: 'header',
                        acceptedValues: [false, 1, 2, 3, 4, 5],
                        options: { inputAttr: { 'aria-label': 'Header' } },
                        }, 'separator',
                        'color', 'background', 'separator',
                        'link', 'separator',
                        'clear', 'codeBlock', 'blockquote', 'separator',
                        'insertTable', 'deleteTable',
                        'insertRowAbove', 'insertRowBelow', 'deleteRow',
                        'insertColumnLeft', 'insertColumnRight', 'deleteColumn',
                    ],
                    },
                    mediaResizing: {
                    enabled: true,
                    },
                }).dxHtmlEditor('instance');
            });
        </script>
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