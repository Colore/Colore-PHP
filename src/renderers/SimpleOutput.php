<?php

namespace Colore\Renderers;

use Colore\Logger;
use Colore\Interfaces\RequestHelper;
use Colore\Interfaces\RenderHelper;

class SimpleOutput implements RenderHelper {
    public function dispatch(RequestHelper &$cro) {
        $template = $cro->getRenderPath();
        $template_file = sprintf('%s/%s', BASEDIR, $template);

        $renderProperties = $cro->getRenderProperties();

        // Hold the variable in the template variable.
        $template = [];

        Logger::debug('Setting render properties [%d]', count($renderProperties));

        foreach ($renderProperties as $propName => $propVal) {
            Logger::debug('Setting render property [%s] to [%s]', $propName, $propVal);

            $template[$propName] = $propVal;
        }

        $template['context'] = $cro->getContextKey();

        require_once $template_file; // NOSONAR
    }
}
