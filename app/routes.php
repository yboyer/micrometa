
<?php

use Symfony\Component\HttpFoundation\Request;

$app->get('/', function () use ($app) {
    $images = $app['dao.image']->findAll();

    return $app['twig']->render('list.html.twig', [
        'images' => $images,
    ]);
});
