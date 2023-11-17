<?php

namespace Colore\Interfaces\Adapters;

interface IRequestAdapterContextAspects {
    public function getRequestContext();

    public function loadContext($contextData);
}
