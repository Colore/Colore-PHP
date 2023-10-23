<?php

namespace Colore\Interfaces\Adapters;

interface IRequestAdapterMagicAspects {
    public function __get($requestVariable);

    public function __set($requestVariable, $requestValue);

    public function __isset($requestVariable);

    public function __unset($requestVariable);
}
