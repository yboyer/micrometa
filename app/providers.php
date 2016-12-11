<?php

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\TranslationServiceProvider;

ErrorHandler::register();
ExceptionHandler::register();

$app['dao.image'] = function () {
    return new YF\DAO\ImageDAO();
};

$app->register(new ValidatorServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new TranslationServiceProvider(), [
    'locale' => 'fr'
]);

// Twig setup
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/../views',
]);
if (( $rootPath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])) ) !== '/') {
    $rootPath .= '/';
}
$app['twig']->addGlobal('root', $rootPath);
