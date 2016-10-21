<?php

namespace YF\Domain;

abstract class Domain
{
    public function __construct(array $data)
    {
        foreach ($data as $attribute => $value) {
            $setter = 'set'.ucfirst($attribute);
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }
}
