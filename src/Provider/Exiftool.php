<?php

namespace YF\Provider;

class Exiftool
{
    public function __construct()
    {
        $bin = '';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $bin = dirname(__FILE__).'/';
        }
        $this->exe = $bin.'exiftool';
    }

    /**
     * Retreive the metadata of the file
     * @return The metadata of the file
     */
    public function getData(string $path): array
    {
        $key = basename($path);

        if (!JsonManager::getInstance()->exists($key)) {
            $data = json_decode(shell_exec("$this->exe $path -json -g"), true)[0];
            if (is_null($data)) {
                $data = [];
            }
            unset($data['Preview']);
            unset($data['ExifTool']);
            unset($data['File']);

            JsonManager::getInstance()->set(
                $key,
                $data
            );
        }

        return JsonManager::getInstance()->get($key);
    }

    /**
     *Set the metadata of the file
     */
    public function setDatas($path, array $data)
    {
        $params ='';
        foreach ($data as $bigIdentifier => $subIdentifier) {
            foreach ($subIdentifier as $realSubIdentifier => $firstValue) {
                if (is_array($firstValue)) {
                    foreach ($firstValue as $subSubIdentifier => $endValue) {
                        $params .= ' -'.$realSubIdentifier.':'.$subSubIdentifier.'='.$endValue;
                    }
                } else {
                    $params .= ' -'.$bigIdentifier.':'.$realSubIdentifier.'='.$firstValue;
                }
            }
            shell_exec("$this->exe $params $path -overwrite_original");
        }
    }

    /**
     * Retreive the content of the XMP Sidecar file
     * @return The content of the XMP Sidecar file
     */
    public function getXMPSidecarContent(string $path)
    {
        return shell_exec("$this->exe -xmp -b $path");
    }
}
