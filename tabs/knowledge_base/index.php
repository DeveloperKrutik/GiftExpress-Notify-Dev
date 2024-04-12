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
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Knowledge base</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../../assets/css/flowbite.css">
        <script src="../../assets/js/tailwind.js"></script>
        <script src="../../assets/js/flowbite.min.js"></script>
        <script src="../../assets/js/jquery.js?v=<?php echo time(); ?>"></script>
        <script src="../../assets/js/ajax.js"></script>
    </head>
    <body>

        <div class="py-5 px-20">
            <div class="border-b border-gray-200">
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
                        <a href="../email_template/index.php?<?php echo http_build_query($requestparams); ?>" class="inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 group">
                            <svg class="w-4 h-4 me-2 text-gray-400 group hover:text-gray-500" viewBox="0 0 24 24" fill="currentColor"><defs><style>.cls-1{opacity:0;}.cls-2{}</style></defs><title>email</title><g id="Layer_2" data-name="Layer 2"><g id="email"><g id="email-2" data-name="email"><rect class="cls-1" width="24" height="24"/><path class="cls-2" d="M19,4H5A3,3,0,0,0,2,7V17a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V7A3,3,0,0,0,19,4Zm-.67,2L12,10.75,5.67,6ZM19,18H5a1,1,0,0,1-1-1V7.25l7.4,5.55a1,1,0,0,0,.6.2,1,1,0,0,0,.6-.2L20,7.25V17A1,1,0,0,1,19,18Z"/></g></g></g></svg>Email Template
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="../knowledge_base/index.php?<?php echo http_build_query($requestparams); ?>" class="inline-flex items-center justify-center p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active group">
                            <svg class="w-4 h-4 me-2 text-blue-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
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

            <div class="grid grid-cols-3 gap-3 container bg-white p-5 rounded-md border border-gray-100 shadow mt-5">
                
              <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow">
                  <a href="blog.php?<?php echo http_build_query($requestparams); ?>&blog=1">
                      <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Create gift notification form</h5>
                  </a>
                  <p class="mb-3 font-normal text-gray-700">How to create gift notification form on the product page.</p>
                  <a href="blog.php?<?php echo http_build_query($requestparams); ?>&blog=1" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-md hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
                      Read more
                      <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                      </svg>
                  </a>
              </div>

              <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow">
                  <a href="blog.php?<?php echo http_build_query($requestparams); ?>&blog=2">
                      <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Setup SMTP configurations</h5>
                  </a>
                  <p class="mb-3 font-normal text-gray-700">How to whitelist your custom gmail SMTP configurations</p>
                  <a href="blog.php?<?php echo http_build_query($requestparams); ?>&blog=2" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-md hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
                      Read more
                      <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                      </svg>
                  </a>
              </div>

              <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow">
                  <a href="blog.php?<?php echo http_build_query($requestparams); ?>&blog=3">
                      <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Customize email template</h5>
                  </a>
                  <p class="mb-3 font-normal text-gray-700">How to customize the email template, you want to use in gift notification mail.</p>
                  <a href="blog.php?<?php echo http_build_query($requestparams); ?>&blog=3" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-md hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
                      Read more
                      <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                      </svg>
                  </a>
              </div>

            </div>

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