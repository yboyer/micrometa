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
    }

    /**
     * Checks if the images exists on the server
     * @return True if the image exists
     */
    private function exists(string $filename): bool
    {
        return file_exists($this->imagesPath.$filename);
    }

    /**
     * Retrieve all images
     * @return An array of images
     */
    public function findAll(): array
    {
        $images = array_values(array_diff(scandir($this->imagesPath), array('..', '.')));

        for ($i = count($images) - 1; $i >= 0; --$i) {
            $images[$i] = $this->getImageWithData($images[$i]);
        }

        return $images;
    }

    /**
     * Return an image containing all its metadata
     * @param $filename The file name to retrieve
     * @return An image with metadata
     */
    private function getImageWithData(string $filename): Image
    {
        $data = (new Exiftool($this->imagesPath.$filename))->getData();

        $image = [
            'filename' => $filename,
            'path' => 'images/'.$filename,
            'data' => $data,
            'name' => $data['XMP-dc']['Title'],
            'author' => $data['XMP-dc']['Creator'],
            'description' => $data['XMP-dc']['Description'],
        ];
        if (isset($data['XMP-iptcCore'])) {
            if (isset($data['XMP-iptcCore']['Location'])) {
                $image['location'] = $data['XMP-iptcCore']['Location'];
            }
        }
        return new Image($image);
    }

    /**
     * Retrieve an image to a given file name
     * @param $filename The given file name
     * @return An image
     */
    public function findOne(string $filename)
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
    public function getXmp(string $filename)
    {
        if (!$this->exists($filename)) {
            return null;
        }

        return (new Exiftool($this->imagesPath.$filename))->getXmp();
    }
}
