<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;


// Sends the list stored files
$app->get('/', function () use ($app) {
    $images = $app['dao.image']->findAll();

    return $app['twig']->render('list.html.twig', [
        'images' => $images,
    ]);
})->bind('list');


// Sends the detail of a given file
$app->get('/detail/{filename}', function (string $filename) use ($app) {
    $image = $app['dao.image']->findOne($filename);

    if ($image == null) {
        $app->abort(404, 'Cette image n\'existe pas');
    }

    return $app['twig']->render('detail.html.twig', [
        'image' => $image,
    ]);
})->bind('detail');


// Downloads a given file
$app->get('/download/{filename}', function (string $filename) use ($app) {
    $image = $app['dao.image']->findOne($filename);

    if ($image == null) {
        $app->abort(404, 'Cette image n\'existe pas');
    }

    $response = new BinaryFileResponse($image->getPath());
    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

    return $response;
})->bind('downloadFile');


// Downloads the XMP Sidecar file of a given file
$app->get('/xmp/{filename}', function (string $filename) use ($app) {
    $xmp = $app['dao.image']->getXMPSidecarContent($filename);

    if ($xmp == null) {
        $app->abort(404, 'Cette image n\'existe pas');
    }

    $response = new Response();
    $response->headers->set('Cache-Control', 'private');
    $response->headers->set('Content-type', 'application-octet-stream');
    $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'.xmp"');
    $response->headers->set('Content-length', count($xmp));
    $response->sendHeaders();
    $response->setContent($xmp);

    return $response;
})->bind('downloadXmp');


// 404 page
$app->error(function (\Exception $e, Request $res, $code) use ($app) {
    switch ($code) {
        case 404:
            return $app['twig']->render('404.html.twig');
    }
});
