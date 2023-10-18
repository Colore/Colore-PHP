<?php

namespace Colore\Renderers;

use Colore\Logger;
use Colore\Interfaces\Adapters\IRequestAdapter;
use Colore\Interfaces\Providers\IRenderProvider;

class HTTPOutputSimpleTemplate implements IRenderProvider {
    /**
     * @return void
     */
    public function dispatch(IRequestAdapter &$cro) {
        $template = $cro->getRenderPath();
        $template_file = sprintf('%s/%s', BASEDIR, $template);

        $renderProperties = $cro->getRenderProperties();

        // Hold the variable in the template variable.

        Logger::debug('Setting render properties [%d]', count($renderProperties));

        foreach ($renderProperties as $propName => $propVal) {
            Logger::debug('Setting render property [%s] to [%s]', $propName, $propVal);

            $template[$propName] = $propVal;
        }

        $template['context'] = $cro->getContextKey();

        ob_start();

        require_once $template_file; // NOSONAR

        $ob = ob_get_contents();

        ob_end_clean();

        $cro->output($ob);
    }
}