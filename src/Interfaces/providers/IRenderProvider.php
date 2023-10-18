<?php

namespace Colore\Interfaces\Providers;

use Colore\Interfaces\Adapters\IRequestAdapter;

interface IRenderProvider {
    public function dispatch(IRequestAdapter &$cro);
}
