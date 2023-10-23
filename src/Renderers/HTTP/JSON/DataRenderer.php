<?php

namespace Colore\Renderers\HTTP\JSON;

use Colore\Logger;
use Colore\Interfaces\Adapters\IRequestAdapter;
use Colore\Interfaces\Providers\IRenderProvider;

class DataRenderer implements IRenderProvider {
    /**
     * @return void
     */
    public function dispatch(IRequestAdapter &$cro) {
        $outputProperties = [];

        $renderProperties = $cro->getRenderProperties();

        Logger::debug('Setting render properties [%d]', count($renderProperties));

        foreach ($renderProperties as $propName => $propVal) {
            Logger::debug('Setting render property [%s] to [%s]', $propName, $propVal);

            $outputProperties[$propName] = $propVal;
        }

        $httpStatusCode = $cro->getRenderArgument('httpStatusCode') ?? 200;

        $output = json_encode($outputProperties);

        $cro->output($output, ['Content-Type' => 'application/json'], $httpStatusCode);
    }
}
