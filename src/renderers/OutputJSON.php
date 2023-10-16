<?php

namespace Colore\Renderers;

use Colore\Logger;
use Colore\Interfaces\RequestHelper;
use Colore\Interfaces\RenderHelper;

class OutputJSON implements RenderHelper {
    public function dispatch(RequestHelper &$cro) {
        $outputProperties = [];

        $renderProperties = $cro->getRenderProperties();

        Logger::debug('Setting render properties [%d]', count($renderProperties));

        foreach ($renderProperties as $propName => $propVal) {
            Logger::debug('Setting render property [%s] to [%s]', $propName, $propVal);

            $outputProperties[$propName] = $propVal;
        }

        echo htmlspecialchars(json_encode($outputProperties));
    }
}
