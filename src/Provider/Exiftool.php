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
     * @param $path The path of the file name
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
     * Set the metadata of the file
     * @param $path The path of the file name
     * @param $data The metadatas to add
     */
    public function setDatas(string $path, array $data)
    {
        $primaryKey = basename($path);

        $params = '';
        foreach ($data as $key => $value) {
            foreach ($value as $subKey => $subValue) {
                if (is_array($subValue)) {
                    foreach ($subValue as $subSubValue) {
                        $params .= " -$key:$subKey=\"$subSubValue\"";
                    }
                } else {
                    $params .= " -$key:$subKey=\"$subValue\"";
                }

                // Update the database
                JsonManager::getInstance()->update(
                    "$primaryKey:$key:$subKey",
                    $subValue
                );
            }
        }
        // Update the file
        shell_exec("$this->exe $params $path -overwrite_original");
        // Write changes into the file
        JsonManager::getInstance()->save();
    }

    /**
     * Retreive the content of the XMP Sidecar file
     * @param $path The path of the file name
     * @return The content of the XMP Sidecar file
     */
    public function getXMPSidecarContent(string $path)
    {
        return shell_exec("$this->exe -xmp -b $path");
    }
}
