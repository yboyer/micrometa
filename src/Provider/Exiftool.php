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

    public function getData(): array
    {
        return json_decode(shell_exec("$this->exe $this->path -json -g1 -xmp:all"), true)[0];
    }

    public function getXmp(): string
    {
        return shell_exec("$this->exe -xmp -b $this->path");
    }
}
