<?php

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

ErrorHandler::register();
ExceptionHandler::register();

$app['dao.image'] = function () {
    return new YF\DAO\ImageDAO();
};

// Twig setup
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/../views',
]);
$app['twig']->addGlobal('root', '/');
