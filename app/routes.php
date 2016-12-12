<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints as Assert;


// Sends the list stored files
$app->get('/', function () use ($app) {
    $images = $app['dao.image']->findAll();

    return $app->render('list.html.twig', [
        'images' => $images,
    ]);
})->bind('list');


// Sends the detail of a given file
$app->get('/detail/{filename}', function (string $filename) use ($app) {
    $image = $app['dao.image']->findOne($filename);

    if ($image == null) {
        return $app->abort(404, 'Cette image n\'existe pas');
    }

    return $app->render('detail.html.twig', [
        'image' => $image,
    ]);
})->bind('detail');


// Downloads a given file
$app->get('/download/{filename}', function (string $filename) use ($app) {
    $image = $app['dao.image']->findOne($filename);

    if ($image == null) {
        return $app->abort(404, 'Cette image n\'existe pas');
    }

    $response = new BinaryFileResponse($image->getPath());
    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

    return $response;
})->bind('downloadFile');


// Downloads the XMP Sidecar file of a given file
$app->get('/xmp/{filename}', function (string $filename) use ($app) {
    $xmp = $app['dao.image']->getXMPSidecarContent($filename);

    if ($xmp == null) {
        return $app->abort(500, 'Impossible d\'extraire le contenu XMP Sidecar');
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
    $form = $app->form()
        ->add('image', FileType::class, [
            'constraints' => new Assert\Image(),
        ])
        ->getForm();

    $form->handleRequest($request);
    if ($request->isMethod('POST') && $form->isValid()) {
        $image = $form['image']->getData();

        // Get an unique id for the file
        do {
            $filename = md5(uniqid()).'.'.$image->guessExtension();
        } while ($app['dao.image']->exists($filename));

        $image->move(
            __DIR__.'/../web/images/',
            $filename
        );

        // Redirect to the update page
        return $app->redirect($app->path('update', [
            'filename' => $filename
        ]));
    }

    return $app->render('upload.html.twig', [
        'form' => $form->createView()
    ]);
}, 'GET|POST')->bind('upload');


// Update
$app->match('/update/{filename}', function (Request $request, string $filename) use ($app) {
    // Retrieve the image from its filename
    $image = $app['dao.image']->findOne($filename);

    // Checks if the image exists
    if ($image == null) {
        return $app->abort(404, 'Cette image n\'existe pas');
    }

    // Create form
    $form = $app->form();

    $updatedMetadatas = [];
    $fixedMetadata = ['MakerNotes', 'Composite'];
    foreach ($image->getData() as $key => $value) {
        if (is_array($value) && !in_array($key, $fixedMetadata)) {
            foreach ($value as $subKey => $subValue) {

                $data = $subValue;
                // Join value into the field
                if (is_array($subValue)){
                    $data = join(', ', $subValue);
                }

                // Add field into the form
                $fieldLabel = "$key --- $subKey";
                $fieldName = str_replace(' ', '', $fieldLabel);
                $form = $form->add($fieldName, TextType::class, [
                        'label' => $fieldLabel,
                        'data' => $data,
                        'required' => false
                ]);

                // Retreive data from the 2nd form
                $data = $form->getForm()
                    ->handleRequest($request)
                    ->getData()[$fieldName];

                // Explode the field if it's supposed to be an array field
                if ($subKey === 'Subject' || $subKey === 'Keywords') {
                    $data = explode(', ', $data);
                }
                $updatedMetadatas[$key][$subKey] = $data;
            }
        }
    }

    if ($request->isMethod('POST')) {
        // Update metadatas
        $app['dao.image']->updateMetadata($filename, $updatedMetadatas);

        // Redirect to the detail page
        return $app->redirect($app->path('detail', [
            'filename' => $filename
        ]));
    }

    // Display the update page
    return $app->render('update.html.twig', [
        'image' => $image,
        'form' => $form->getForm()->createView(),
    ]);
}, 'GET|POST')->bind('update');


// 404 page
$app->error(function (\Exception $e, Request $res, $code) use ($app) {
    $message = $e->getMessage();
    if (strpos($message, 'No route found') !== false) {
        $message = 'Page introuvable';
    }

    switch ($code) {
        case 500:
        case 404:
            return $app->render('error.html.twig', [
                'code' => $code,
                'message' => $message
            ]);
    }
});
