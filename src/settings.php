<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
		//Db
		'db' => [
            'driver' => 'mysql',
            'host' => 'localhost:3307',
            'database' => 'member',
            'username' => 'member',
            'password' => 'member',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => 'member_',
        ],
		//dayu
		'alidayu' => [
			'app_key'    => '23517461',
			'app_secret' => 'a8306f3425f43fc81b181c3c702454f6',
			// 'sandbox'    => true,  // 是否为沙箱环境，默认false
		],
		
    ],
];
