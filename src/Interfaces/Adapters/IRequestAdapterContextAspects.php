<?php

namespace Colore\Interfaces\Adapters;

interface IRequestAdapterContextAspects {
    public function getContextKey();

    public function loadContext($contextData);
}
