<?php

namespace Colore\Providers\InMemory;

use Colore\Interfaces\Providers\IContextProvider;
use Colore\Logger;

use MNC\PathToRegExpPHP\PathRegExpFactory;

class RouteContextProvider implements IContextProvider {
    protected $config = [];
    protected array $contexts = [];
    protected $contextCache = [];

    public function __construct($config) {
        $this->parseRoutes($config);
    }

    private function parseRoutes($config) {
        if (!isset($config['context']['default']) && !isset($config['context']['error'])) {
            throw new \Error('Missing default and error context definitions');
        }

        foreach ($config['context'] as $contextKey => $contextValue) {
            if (!isset($this->contexts[$contextKey])) {
                $this->contexts[$contextKey] = [];
            }

            $contextMethod = isset($contextValue['method']) ? $contextValue['method'] : 'all';

            $contextValue['matcher'] = PathRegExpFactory::create($contextKey);

            $this->contexts[$contextKey][$contextMethod] = $contextValue;
        }
    }

    public function resolveContext($request_context, $request_method = 'all') {
        // Corce to contextRequestObject
        if (!is_array($request_context)) {
            $request_context = [
                'uri' => $request_context,
                'method' => $request_method ?? 'all'
            ];
        }

        // Check if the requested context is empty.
        // Return the default context if so
        if ($request_context['uri'] === '') {
            return isset($this->config['contexts']['default'][$request_context['method']])
                ? $this->config['contexts']['default'][$request_context['method']]
                : $this->config['contexts']['default']['all'];
        }

        // Lookup result placeholder
        $lookupResult = null;
        $contextName = '';
        $contextMethod = '';

        // Loop over the contexts and save the matches
        foreach ($this->config['contexts'] as $contextKey => $contextGroup) {
            if (isset($contextGroup[$request_context['method']])) {
                $contextObj = $contextGroup[$request_context['method']];

                try {
                    $lookupResult = $contextObj->match($request_context['uri']);
                    $contextName = $contextKey;
                    $contextMethod = $request_context['uri'];
                    break;
                } catch (\Exception $err) {
                    //
                }
            } else {
                try {
                    $lookupResult = $contextGroup['all']->match($request_context['uri']);
                    $contextName = $contextKey;
                    $contextMethod = 'all';
                    break;
                } catch (\Exception $err) {
                    //
                }
            }

            if (!is_null($lookupResult)) {
                break;
            }
        }

        // Check if lookupResult returns a valid context, else fall back to 'error'
        if (empty($contextName)) {
            $contextName = 'error';

            Logger::debug('falling back to error');
        }

        Logger::debug('resolved to: %s', $contextName);

        $contextResult = $this->config['contexts'][$contextName][$contextMethod];

        if (!is_null($lookupResult) && count($lookupResult->getValues()) > 0) {
            $contextResult['properties'] = array_merge($contextResult['properties'], $lookupResult->getValues());
        }

        // Return the resolved context
        return $contextResult;
    }
}
