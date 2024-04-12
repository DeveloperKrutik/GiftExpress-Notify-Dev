<?php

    include_once('inc/functions.php');
    include_once('inc/variable.php');
    include_once('config/common.php');

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

            $access_token = $user[0]['token'];

            if(($user[0]['webhook_id'] == '') || ($user[0]['webhook_id'] == 0)){
                $wid = send_email_webhook($user[0]['id'], $access_token, $shopname);
                
                $updatewebhook = "UPDATE users SET webhook_id = '".$wid."' WHERE id = '".$user[0]['id']."' ";
                $obj->edit($updatewebhook);
            }

            $weeklydatasql = "SELECT COUNT(*) AS cnt, userdate FROM emails WHERE userdate BETWEEN CURDATE() - INTERVAL 6 DAY AND CURDATE() GROUP BY userdate";
            $weeklydata = $obj->select($weeklydatasql);
            $weekdays = array();
            $weekcnt = array();
            
            foreach ($weeklydata as $day) {
                array_push($weekdays, $day['userdate']);
                array_push($weekcnt, $day['cnt']);
            }

            $monthscnt = array(0,0,0,0,0,0,0,0,0,0,0,0);
            
            $monthlydatasql = "SELECT 
                    MONTH(userdate) AS month,
                    COUNT(*) AS record_count
                FROM emails
                WHERE YEAR(userdate) = YEAR(CURRENT_DATE)
                GROUP BY MONTH(userdate)
                ORDER BY month";
            $monthlydata = $obj->select($monthlydatasql);
            
            foreach ($monthlydata as $month) {
                $monthscnt = array_replace($monthscnt, array($month[0]-1 => $month[1]));
            }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Gift Notification</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="assets/css/flowbite.css">
        <script src="assets/js/tailwind.js"></script>
        <script src="assets/js/flowbite.min.js"></script>
        <script src="assets/js/jquery.js?v=<?php echo time(); ?>"></script>
        <script src="assets/js/ajax.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    </head>
    <body>

        <div class="py-5 px-20">
            <div class="border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500">
                    <li class="me-2">
                        <a href="index.php?<?php echo http_build_query($requestparams); ?>" class="inline-flex items-center justify-center p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active group" aria-current="page">
                            <svg class="w-4 h-4 me-2 text-blue-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 18">
                                <path d="M6.143 0H1.857A1.857 1.857 0 0 0 0 1.857v4.286C0 7.169.831 8 1.857 8h4.286A1.857 1.857 0 0 0 8 6.143V1.857A1.857 1.857 0 0 0 6.143 0Zm10 0h-4.286A1.857 1.857 0 0 0 10 1.857v4.286C10 7.169 10.831 8 11.857 8h4.286A1.857 1.857 0 0 0 18 6.143V1.857A1.857 1.857 0 0 0 16.143 0Zm-10 10H1.857A1.857 1.857 0 0 0 0 11.857v4.286C0 17.169.831 18 1.857 18h4.286A1.857 1.857 0 0 0 8 16.143v-4.286A1.857 1.857 0 0 0 6.143 10Zm10 0h-4.286A1.857 1.857 0 0 0 10 11.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 18 16.143v-4.286A1.857 1.857 0 0 0 16.143 10Z"/>
                            </svg>Dashboard
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="tabs/smtp/index.php?<?php echo http_build_query($requestparams); ?>" class="inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 group">
                            <svg class="w-4 h-4 me-2 text-gray-400 group-hover:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 11.424V1a1 1 0 1 0-2 0v10.424a3.228 3.228 0 0 0 0 6.152V19a1 1 0 1 0 2 0v-1.424a3.228 3.228 0 0 0 0-6.152ZM19.25 14.5A3.243 3.243 0 0 0 17 11.424V1a1 1 0 0 0-2 0v10.424a3.227 3.227 0 0 0 0 6.152V19a1 1 0 1 0 2 0v-1.424a3.243 3.243 0 0 0 2.25-3.076Zm-6-9A3.243 3.243 0 0 0 11 2.424V1a1 1 0 0 0-2 0v1.424a3.228 3.228 0 0 0 0 6.152V19a1 1 0 1 0 2 0V8.576A3.243 3.243 0 0 0 13.25 5.5Z"/>
                            </svg>SMTP
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="tabs/email_template/index.php?<?php echo http_build_query($requestparams); ?>" class="inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 group">
                            <svg class="w-4 h-4 me-2 text-gray-400 group-hover:text-gray-500" viewBox="0 0 24 24" fill="currentColor"><defs><style>.cls-1{opacity:0;}.cls-2{}</style></defs><title>email</title><g id="Layer_2" data-name="Layer 2"><g id="email"><g id="email-2" data-name="email"><rect class="cls-1" width="24" height="24"/><path class="cls-2" d="M19,4H5A3,3,0,0,0,2,7V17a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V7A3,3,0,0,0,19,4Zm-.67,2L12,10.75,5.67,6ZM19,18H5a1,1,0,0,1-1-1V7.25l7.4,5.55a1,1,0,0,0,.6.2,1,1,0,0,0,.6-.2L20,7.25V17A1,1,0,0,1,19,18Z"/></g></g></g></svg>Email Template
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="tabs/knowledge_base/index.php?<?php echo http_build_query($requestparams); ?>" class="inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 group">
                            <svg class="w-4 h-4 me-2 text-gray-400 group-hover:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z"/>
                            </svg>Knowledge base
                        </a>
                    </li>
                    <li class="me-2">
                        <a href="tabs/contact_us/index.php?<?php echo http_build_query($requestparams); ?>" class="inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 group">
                            <svg class="w-4 h-4 me-2 text-gray-400 group-hover:text-gray-500" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21 8V7l-3 2-3-2v1l3 2 3-2zm1-5H2C.9 3 0 3.9 0 5v14c0 1.1.9 2 2 2h20c1.1 0 1.99-.9 1.99-2L24 5c0-1.1-.9-2-2-2zM8 6c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm6 12H2v-1c0-2 4-3.1 6-3.1s6 1.1 6 3.1v1zm8-6h-8V6h8v6z"/></svg>Contact us
                        </a>
                    </li>
                </ul>
            </div>

            <!-- <a href="create_webhook.php" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">Create webhook</a>
            <a href="remove_webhook.php?wh=1237195325621" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">Remove webhook</a> -->
            

            <div class="inline-flex w-full">
                <div class="px-10 py-5 my-5 mr-1.5 bg-white rounded-md border border-gray-100 shadow w-1/2 h-fit">
                    <canvas id="yearly" class="h-full"></canvas>
                </div>
                <div class="px-10 py-5 my-5 ml-1.5 bg-white rounded-md border border-gray-100 shadow w-1/2 h-fit">
                    <canvas id="weekly" class="h-full"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 container bg-white p-5 rounded-md border border-gray-100 shadow">
                <p class="font-medium">
                    Steps to setup gift notifications:
                </p>
                <a href="tabs/knowledge_base/blog.php?<?php echo http_build_query($requestparams); ?>&blog=1" class="ml-3 text-blue-500 text-sm my-1 hover:text-blue-600 hover:cursor-pointer hover:underline">Create form using LineItem properties on product page</a>
                <a href="tabs/knowledge_base/blog.php?<?php echo http_build_query($requestparams); ?>&blog=2" class="ml-3 text-blue-500 text-sm my-1 hover:text-blue-600 hover:cursor-pointer hover:underline">Whitelist your gmail SMTP configurations</a>
                <a href="tabs/knowledge_base/blog.php?<?php echo http_build_query($requestparams); ?>&blog=3" class="ml-3 text-blue-500 text-sm my-1 hover:text-blue-600 hover:cursor-pointer hover:underline">Customize the email template</a>
                
                <a href="knowledge_base.php?<?php echo http_build_query($requestparams); ?>" class="font-medium inline-flex text-blue-500 text-sm mt-2 hover:text-blue-600 hover:cursor-pointer hover:underline">Learn more...</a>
            </div>

            <script>
                var currentyear = `<?php echo date('Y'); ?>`;
                var xValues = [`Jan`, `Feb`, `Mar`, `Apr`, `May`, `Jun`, `Jul`, `Aug`, `Sep`, `Oct`, `Nov`, `Dec`];
                var yValues = <?php echo json_encode($monthscnt); ?>;
                var barColors = Array(12).fill('#4ebfba');

                new Chart("yearly", {
                    type: "bar",
                    data: {
                        labels: xValues,
                        datasets: [{
                        backgroundColor: barColors,
                        data: yValues
                        }]
                    },
                    options: {
                        legend: {display: false},
                        title: {
                        display: true,
                        text: `Emails sent in ${currentyear} (month wise)`
                        }
                    }
                });

                var xValues = <?php echo json_encode($weekdays); ?>;
                var yValues = <?php echo json_encode($weekcnt); ?>;
                var barColors = Array(12).fill('#4ebfba');

                new Chart("weekly", {
                    type: "bar",
                    data: {
                        labels: xValues,
                        datasets: [{
                        backgroundColor: barColors,
                        data: yValues
                        }]
                    },
                    options: {
                        legend: {display: false},
                        title: {
                        display: true,
                        text: "Emails sent in last 7 days (day wise)"
                        }
                    }
                });
            </script>

        </div>

    </body>
</html>
<?php
        }else{
            $install_url = "install.php?shop=".$shopname."";
            header("Location: " . $install_url);
            die();
        }
    }else{
        echo "Something went wrong, please try again!";
    }
?>