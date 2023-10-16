<?php

namespace Colore\Renderers;

use Smarty\Smarty;
use Colore\Logger;
use Colore\Interfaces\RequestHelper;
use Colore\Interfaces\RenderHelper;

require_once 'vendor/autoload.php';

class RendererSmarty implements RenderHelper {
    public function dispatch(RequestHelper &$cro) {
        /**
         * Create new Smarty object
         */
        $smarty = new Smarty();

        /**
         * Get the template file from the assigned render path.
         */
        $template = $cro->getRenderPath();

        /**
         * Clear all of the assigned variables.
         */
        $smarty->clearAllAssign();

        /**
         * Get all of the render properties.
         */
        $renderProperties = $cro->getRenderProperties();

        Logger::debug('Setting render properties [%d]', count($renderProperties));

        /**
         * Iterate over all the render properties and assign them to the Smarty instance.
         */
        foreach ($renderProperties as $propName => $propVal) {
            Logger::debug('Setting render property [%s] to [%s]', $propName, $propVal);

            $smarty->assign($propName, $propVal);
        }

        /**
         * Render (display) the result.
         */
        $smarty->display($template);
    }
}
