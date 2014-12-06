<?php

require_once __DIR__.'/../app/config.php';

$app->get('/', function () use ($app) {
	 echo '<h1>Welcome buddy !</h1>';
})->name('root');

$app->run();

?>
