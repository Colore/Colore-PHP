<?php

namespace Colore\Renderers\HTTP;

use Colore\Logger;
use Colore\Interfaces\Adapters\IRequestAdapter;
use Colore\Interfaces\Providers\IRenderProvider;

class HTTPSimpleTemplateRenderer implements IRenderProvider {
    /**
     * @return void
     */
    public function dispatch(IRequestAdapter &$cro) {
        $template_file = sprintf('%s/%s', BASEDIR, $cro->getRenderPath());

        $renderProperties = $cro->getRenderProperties();

        // Hold the template variables in the template variable.
        $template = [];

        Logger::debug('Setting render properties [%d]', count($renderProperties));

        foreach ($renderProperties as $propName => $propVal) {
            Logger::debug('Setting render property [%s] to [%s]', $propName, $propVal);

            $template[$propName] = $propVal;
        }

        $template['context'] = $cro->getContextKey();

        $httpHeaders = $cro->getRenderArgument('httpHeaders') ?? [];
        $httpStatusCode = $cro->getRenderArgument('httpStatusCode') ?? 200;

        ob_start();

        require_once $template_file; // NOSONAR

        $ob = ob_get_contents();

        ob_end_clean();

        $cro->output($ob, $httpHeaders, $httpStatusCode);
    }
}
