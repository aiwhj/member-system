<?php
// DIC configuration
$container = $app->getContainer();
//数据验证
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('Page not foundttt');
    };
};
$container['cache'] = function ($c) {
	$cache = new Gregwar\Cache\Cache;
	$cache->setCacheDirectory('cache/cache');
	return $cache;
};
$container['violin'] = function ($c) {
	$violin = new \Violin\Violin;
	return $violin;
};
//session 处理
$container['session'] = function ($c) {
  return new SlimSession\Helper;
};
$container['redis'] = function ($c) {
  return  new Predis\Client();
};
//twig模板引擎
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig('templates', [
        
    ]);
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));
    $view->addExtension(new MemberExtension($c));
    return $view;
};
//excel处理类库
$container['excel'] = function ($c) {
    return new PHPExcel();
};
// monolog  日志
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};
//Db 数据库
$container['db'] = function ($c) {
	$settings = $c->get('settings')['db'];
    $capsule = new Illuminate\Database\Capsule\Manager;
	$capsule->addConnection($settings);
	$capsule->setEventDispatcher(new Illuminate\Events\Dispatcher(new Illuminate\Container\Container));
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    return $capsule;
};
$container['alidayu'] = function ($c) {
	$settings = $c->get('settings')['alidayu'];
	$sendSms = new Dankal\DkAlidayu\AlidayuSms($settings['app_key'], $settings['app_secret']);
	$sendSms->setSignName("开心哈哈哈");
	$sendSms->setTemplateCode("SMS_25305126");
	return $sendSms;
};
