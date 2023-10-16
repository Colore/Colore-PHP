<?php

namespace Colore\Helpers;

use Colore\ConfigStore;
use Colore\Interfaces\ContextHelper;
use Colore\Logger;

class PHPContextHelper implements ContextHelper {
    protected $config;

    public function __construct() {
        $this->config = ConfigStore::getContextConfig();
    }

    public function resolveContext($requested_context) {
        // Check if the requested context is empty.
        // Return the default context if so
        if ($requested_context === '') {
            return $this->config['default'];
        }

        // Set up a variable to hold the results
        $lookup_result = '';

        // Loop over the contexts and save the matches
        foreach (array_keys($this->config) as $context_handle) {
            /** If the context_handle is small enough to match the requested_context,
             * the context_handle matches the requested_context
             * and the match is longer than the saved lookup_result,
             * then save it */
            if (
                strlen($context_handle) <= strlen($requested_context) &&
                substr($requested_context, 0, strlen($context_handle)) == $context_handle &&
                strlen($context_handle) > strlen($lookup_result)
            ) {
                $lookup_result = $context_handle;
            }
        }

        // Check if lookup_result returns a valid context, else fall back to 'error'
        if (!isset($this->config[$lookup_result]) || !is_array($this->config[$lookup_result])) {
            $lookup_result = 'error';
        }

        Logger::debug('lookup_result: %s', $lookup_result);
        Logger::debug('lookup_result: %s', json_encode($this->config[$lookup_result]));

        // Return the right context
        return $this->config[$lookup_result];
    }
}
