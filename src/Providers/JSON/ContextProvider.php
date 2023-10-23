<?php

namespace Colore\Providers\JSON;

use Colore\Logger;
use Colore\Interfaces\Providers\IContextProvider;

class ContextProvider implements IContextProvider {
    /**
     * @return void
     */
    public function resolveContext($contextKey) {
        /**
         * We generate the filename using basename to strip off any unwanted directory insertions,
         * then finally escape special characters.
         */
        $jsonFile = addslashes(basename(sprintf('%s.json', $contextKey)));

        $contextFile = sprintf('%s/contexts/json/%s', BASEDIR, $jsonFile);

        Logger::debug('Trying for context [%s]', $contextKey);

        /**
         * Try the file and revert to the default file if needed.
         */
        if (empty($contextKey) || !file_exists($contextFile) || !file_get_contents($contextFile)) {
            $contextFile = sprintf('%s/contexts/json/%s', BASEDIR, 'default.json');
        }

        /**
         * Open and decode the file, and then return it.
         */
        Logger::debug(' Returning contextFile: [%s]', $contextFile);

        $context = json_decode(file_get_contents($contextFile), true);

        var_dump($context);
    }
}
