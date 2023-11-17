<?php

namespace Colore\Interfaces\Adapters;

interface IRequestAdapterLogicAspects {
    public function hasException();

    public function doException();

    public function appendLogic(array $logic);

    public function insertLogic(array $logic);

    public function getLogic();

    public function getNextLogic();
}
