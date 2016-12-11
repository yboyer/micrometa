<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


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
        $app->abort(500, 'Impossible d\'extraire le contenu XMP Sidecar');
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


// Upload
$app->match('/upload', function (Request $request) use ($app) {
    $form = $app['form.factory']
        ->createBuilder(FormType::class)
        ->add('image', FileType::class)
        ->getForm()
    ;

    $form->handleRequest($request);

    if ($request->isMethod('POST')) {
        if ($form->isValid()) {
            $image = $form['image']->getData();

            $image->move(
                __DIR__.'/../web/images/',
                $image->getClientOriginalName()
            );
        }
    }

    return $app['twig']->render('upload.html.twig', [
        'form' => $form->createView()
    ]);
}, 'GET|POST')->bind('upload');


// Seconds step of upload
$app->post('/uploadStep2', function (Request $request) use ($app) {
    $isStep3 = false;

    $image = $app['form.factory']
        ->createBuilder(FormType::class)
        ->add('image', FileType::class)
        ->getForm()
        ->handleRequest($request)['image']
        ->getData();

    $formMetaStep2 = $app['form.factory']->createBuilder(FormType::class);
    if (!is_null($image)) { // Step 2
        // Upload the image
        $image->move(
            __DIR__.'/../web/images/',
            $image->getClientOriginalName()
        );
        $filename = $image->getClientOriginalName();

        // Add field to retrieve the filename in the step 3
        $formMetaStep2 = $formMetaStep2->add('filename', HiddenType::class, [
            'label' => 'filename',
            'data' => $filename
        ]);
    } else { // Step 3
        $isStep3 = true;
        // Retreive filename from the step2 form
        $filename = $app['form.factory']
            ->createBuilder(FormType::class)
            ->add('filename', HiddenType::class)
            ->getForm()
            ->handleRequest($request)['filename']
            ->getData();
    }

    // Retreive metadatas from the image
    $image = $app['dao.image']->findOne($filename);

    $step3Metadata = [];
    foreach ($image->getData() as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $subKey => $subValue) {
                if (is_array($subValue)){
                    foreach ($subValue as $subSubKey => $subSubValue) {
                        $formMetaStep2 = $formMetaStep2
                            ->add($subSubKey, TextType::class, [
                                'label' => $key.' --- '.$subKey.' --- '. $subSubKey,
                                'data' => $subSubValue,
                                'required' => false
                            ]);
                        // Retreive data from the 2nd form
                        $formMetaStep3 = $formMetaStep2->getForm()->handleRequest($request);
                        $step3Metadata[$key][$subKey][$subSubKey] = $formMetaStep3->getData()[$subSubKey];
                    }
                } else {
                    $formMetaStep2 = $formMetaStep2
                        ->add($subKey, TextType::class, [
                            'label' => $key.' --- '.$subKey,
                            'data' => $subValue,
                            'required' => false
                        ]);
                    // Retreive data from the 2nd form
                    $formMetaStep3 = $formMetaStep2->getForm()->handleRequest($request);
                    $step3Metadata[$key][$subKey] = $formMetaStep3->getData()[$subKey];
                }
            }
        }
    }

    if (!$isStep3) {
        return $app['twig']->render('uploadStep2.html.twig', [
            'image' => $image,
            'form' => $formMetaStep2->getForm()->createView(),
        ]);
    } else {
        // Récupérer les données modifiées dans le form
        // var_dump($step3Metadata);

        // Utiliser exiftool pour modifier les données
        $app['dao.image']->updateMetadata($filename, $step3Metadata);

        return $app->redirect('/');
    }
})->bind('uploadStep2');


// 404 page
$app->error(function (\Exception $e, Request $res, $code) use ($app) {
    $message = $e->getMessage();
    if (strpos($message, 'No route found') !== false) {
        $message = 'Page introuvable';
    }

    switch ($code) {
        case 500:
        case 404:
            return $app['twig']->render('error.html.twig', [
                'code' => $code,
                'message' => $message
            ]);
    }
});
