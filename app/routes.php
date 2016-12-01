<?php

use Symfony\Component\HttpFoundation\Request;

$app->get('/', function () use ($app) {
    $images = $app['dao.image']->findAll();

    return $app['twig']->render('list.html.twig', [
        'images' => $images,
    ]);
});

$app->get('/detail/{filename}', function (string $filename) use ($app) {
    $image = $app['dao.image']->findOne($filename);

    if ($image == null) {
        $app->abort(404, 'Cette image n\'existe pas');
    }

    return $app['twig']->render('detail.html.twig', [
        'image' => $image,
    ]);
});

$app->error(function (\Exception $e, Request $res, $code) use ($app) {
    switch ($code) {
        case 404:
            return $app['twig']->render('404.html.twig');
    }
});
