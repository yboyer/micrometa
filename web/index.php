<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../app/App.php';

$app = new App();

require __DIR__.'/../app/config/dev.php';
require __DIR__.'/../app/providers.php';
require __DIR__.'/../app/routes.php';

$app->run();
