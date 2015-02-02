<?php
// 特定のURLへのアクセスの禁止
\Config::load('access', true);
$urlList = \Config::get('access.ban_url_list');
$except_url_list = \Config::get('access.except_ban_url_list');

$maintenance_active = false;

$current_url = getenv('HTTPS') ? 'https://' : 'http://'.getenv('SERVER_NAME').getenv('REQUEST_URI');
foreach ($urlList as $url) {
    if(strpos($current_url, $url) !== false) {
        if(false === (bool)array_filter($except_url_list,function($except) use ($current_url){ return strpos($current_url, $except); }))
            $maintenance_active = true;
    }
}

return array(
    'fuelphp' => array(
        'app_created' => function()
        {
            // After FuelPHP initialised
        },
        'request_created' => function() use ($maintenance_active)
        {
            // After Request forged
            if ($maintenance_active)
            {
                $data = array(
                    'title' => '404 - Page Not Found',
                    'content' => render('404'),
                );
                // Set a HTTP 404 output header
                return \Response::forge(render('template', $data, false), 404)->send(true);
            }
        },
        'request_started' => function() use ($maintenance_active)
        {
            // Request is requested
            if ($maintenance_active)
            {
                exit;
            }
        },
        'controller_started' => function()
        {
            // Before controllers before() method called
        },
        'controller_finished' => function()
        {
            // After controllers after() method called
        },
        'response_created' => function()
        {
            // After Response forged
        },
        'request_finished' => function()
        {
            // Request is complete and Response received
        },
        'shutdown' => function()
        {
            // Output has been send out
        },
    ),
);
