<?php

namespace YF\DAO;

use YF\Domain\Image;
use YF\Provider\Exiftool;

class ImageDAO
{
    private $imagesPath;

    public function __construct()
    {
        $this->imagesPath = dirname(__FILE__).'/../../web/images/';
        $this->exiftool = new Exiftool();
    }

    /**
     * Checks if the images exists on the server
     * @return True if the image exists
     */
    public function exists($filename)
    {
        return file_exists($this->imagesPath.$filename);
    }

    /**
     * Retrieve all images
     * @return An array of images
     */
    public function findAll()
    {
        $images = array_values(array_diff(scandir($this->imagesPath), array('..', '.')));

        for ($i = count($images) - 1; $i >= 0; --$i) {
            $images[$i] = $this->getImageWithData($images[$i]);
        }

        return $images;
    }

    /**
     * Call exiftool with datas to insert into the image
     */
    public function updateMetadata($filename, $metadata)
    {
        return $this->exiftool->setDatas($this->imagesPath.$filename, $metadata);
    }

    /**
     * Return an image containing all its metadata
     * @param $filename The file name to retrieve
     * @return An image with metadata
     */
    private function getImageWithData($filename)
    {
        $data = $this->exiftool->getData($this->imagesPath.$filename);

        $image = [
            'filename' => $filename,
            'path' => 'images/'.$filename,
            'data' => $data
        ];
        if (isset($data['XMP'])) {
            if (isset($data['XMP']['Title'])) {
                $image['name'] = $data['XMP']['Title'];
            }
            if (isset($data['XMP']['Creator'])) {
                $image['author'] = $data['XMP']['Creator'];
            }
            if (isset($data['XMP']['Description'])) {
                $image['description'] = $data['XMP']['Description'];
            }

            if (isset($data['XMP']['Location'])) {
                $image['location'] = $data['XMP']['Location'];
            }
            if (isset($data['XMP']['City'])) {
                $image['location'] = $data['XMP']['City'];

                if (isset($data['XMP']['Country'])) {
                    $image['location'] .= ', '.$data['XMP']['Country'];
                }
            }
        } else {
            $image['name'] = $filename;
        }
        return new Image($image);
    }

    /**
     * Retrieve an image to a given file name
     * @param $filename The given file name
     * @return An image
     */
    public function findOne($filename)
    {
        if (!$this->exists($filename)) {
            return null;
        }

        return $this->getImageWithData($filename);
    }

    /**
     * Retrieve the XMP Sidecar file content of a given file name
     * @param $filename The given file name
     * @return The XMP Sidecar file
     */
    public function getXMPSidecarContent($filename)
    {
        if (!$this->exists($filename)) {
            return null;
        }

        return $this->exiftool->getXMPSidecarContent($this->imagesPath.$filename);
    }
}
