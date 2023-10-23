<?php

namespace Colore\Interfaces\Adapters;

interface IRequestAdapterLogicAspects {
    public function hasException();

    public function doException();

    public function getLogic();

    public function getNextLogic();
}
