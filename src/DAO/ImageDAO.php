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

    public function findAll()
    {
        $images = array_values(array_diff(scandir($this->imagesPath), array('..', '.')));

        for ($i = count($images) - 1; $i >= 0; --$i) {
            $images[$i] = [
                'filename' => $images[$i],
                'path' => 'images/'.$images[$i],
                'data' => (new Exiftool($this->imagesPath.$images[$i]))->getData(),
            ];
            $images[$i]['name'] = $images[$i]['data']['XMP-dc']['Title'];
            $images[$i]['author'] = $images[$i]['data']['XMP-dc']['Creator'];
            $images[$i] = new Image($images[$i]);
        }

        return $images;
    }

    public function findOne($filename)
    {
        $data = (new Exiftool($this->imagesPath.$filename))->getData();

        if ($data == null) {
            return;
        }

        return new Image([
            'filename' => $filename,
            'path' => 'images/'.$filename,
            'data' => $data,
        ]);
    }
}
