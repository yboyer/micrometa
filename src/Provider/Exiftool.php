<?php

namespace YF\Provider;

class Exiftool
{
    public function __construct(string $path)
    {
        $this->path = $path;
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
    public function getData(): array
    {
        $filename = basename($this->path);

        if (!JsonManager::getInstance()->exists($filename)) {
            JsonManager::getInstance()->set(
                $filename,
                json_decode(shell_exec("$this->exe $this->path -json -g1 -xmp:all"), true)[0]
            );
        }

        return JsonManager::getInstance()->get($filename);
    }

    /**
     * Retreive the content of the XMP Sidecar file
     * @return The content of the XMP Sidecar file
     */
    public function getXmp(): string
    {
        return shell_exec("$this->exe -xmp -b $this->path");
    }
}
