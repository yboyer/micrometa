<?php

namespace YF\Provider;

class Exiftool
{
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->bin = '';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->bin = dirname(__FILE__).'/';
        }
    }

    public function getData(): array
    {
        $exe = $this->bin.'exiftool';

        return json_decode(shell_exec("$exe $this->path -json -g1 -xmp:all"), true)[0];
    }
}
