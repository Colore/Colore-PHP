<?php

namespace Colore\Interfaces;

use Colore\Interfaces\RequestHelper;

interface RenderHelper {
    public function dispatch(RequestHelper &$cro);
}
