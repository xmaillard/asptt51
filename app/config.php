<?php

// Quelques constantes
define('ROOT_PATH', realpath(str_replace("\\", "/", realpath(dirname(__FILE__))) . '/../') . '/');
define('VENDOR_PATH', ROOT_PATH . 'vendor/');
define('PUBLIC_PATH', ROOT_PATH . 'public/');
define('APP_PATH', ROOT_PATH . 'app/');
define('LOG_PATH', APP_PATH . 'var/log');
define('CACHE_PATH', APP_PATH . 'var/cache/');

require_once __DIR__.'/../vendor/autoload.php';

date_default_timezone_set('Europe/Paris');

session_cache_limiter(false);
session_start();

// Notre instance de slim
$app = new \Slim\Slim(
    array(
	'templates.path' => APP_PATH . 'templates/',
	'debug' => true,
	'view' => new \Slim\Views\Twig(),
	'log.enabled' => true,
	'log.level' => \Slim\Log::DEBUG,
	'log.writer' => new \Slim\Extras\Log\DateTimeFileWriter( array(
            'path' => LOG_PATH,
            'name_format' => 'Y-m-d',
            'message_format' => '%label% - %date% - %message%'
        ))
));

$resourceUri = $_SERVER['REQUEST_URI'];
$rootUri = $app->request()->getRootUri();
$assetUri = $rootUri;

// Notre setup twig
$app->view()->appendData(
    array('app' => $app,
          'rootUri' => $rootUri,
          'assetUri' => $assetUri,
          'resourceUri' => $resourceUri
    ));

$view = $app->view();
$view->parserOptions = array(
    'debug' => true,
    'cache' => CACHE_PATH,
    'auto_reload' => true,
    //'strict_variables' => true
);

$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);

/* // Twig Configuration
   \Slim\Extras\Views\Twig::$twigDirectory = VENDOR_PATH . 'Twig';
   \Slim\Extras\Views\Twig::$twigOptions = array("debug" => true);
   if (is_writable(ROOT_PATH . 'cache')) {
   \Slim\Extras\Views\Twig::$twigOptions['cache'] = ROOT_PATH . 'cache';
   } */

$app->hook('slim.after.router', function () use ($app) {
    $request = $app->request;
    $response = $app->response;

    $app->log->debug('Request path: ' . $request->getPathInfo());
    $app->log->debug('Response status: ' . $response->getStatus());
    // And so on ...
});


return $app;

?>
