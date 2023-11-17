<?php

namespace Colore;

use Colore\Logger;
use Colore\Interfaces\Adapters\IRequestAdapter;

use const Colore\Constants\CONTEXT_ADAPTER_INTERFACE;
use const Colore\Constants\CONTEXT_PROVIDER_INTERFACE;

class Engine {
    /**
     * $_objectCache holds the cached class instances as used throughout Colore.
     * Such objects include logic and rendering engines.
     *
     * @var array
     */
    private $objectCache = [];
    /**
     * $_config is the internal configuration store.
     * @var unknown
     */
    private $config = [];
    /**
     * $_helpers holds instances of the helpers used to handle a request.
     * @var unknown
     */
    private $helpers = [];

    /**
     * ColoreEngine is initialized with the initial configuration.
     * @param array $config
     */
    public function __construct(array $config) {
        /**
         * If $config is an array; die on failure.
         */
        if (!is_array($config)) {
            Logger::fatal('Error initializing Colore configuration');
        }

        $this->config = $config;

        $this->loadContextProvider();
    }

    /**
     * Load the context provider(s).
     *
     * @return void
     */
    private function loadContextProvider() {
        $contextConfig = $this->config['helpers']['context'];

        $contextHelper = null;

        if (is_string($contextConfig)) {
            $contextHelper = $this->factory($contextConfig, CONTEXT_PROVIDER_INTERFACE, null);
        } elseif (is_array($contextConfig) && isset($contextConfig['name']) && isset($contextConfig['args'])) {
            $contextHelper = $this->factory($this->config['helpers']['context']['name'], CONTEXT_PROVIDER_INTERFACE, $contextConfig['args']); // NOSONAR
        } else {
            Logger::error('Failed to find a valid context!');
        }

        if (is_null($contextHelper)) {
            Logger::fatal('Failed to acquire context helper');
        }

        $this->helpers['context'] = $contextHelper;
    }

    /**
     * Service handles new requests.
     */
    public function service(...$args): void {
        /**
         * Load the request helper.
         */
        $requestObject = $this->factory($this->config['helpers']['request'], CONTEXT_ADAPTER_INTERFACE, ...$args);

        if (!$requestObject) {
            Logger::fatal('Failed to acquire request helper');
        }

        /**
         * Load default render properties into Request Object.
         */
        $defaultRenderProperties = $this->config['defaults']['render']['properties'];

        foreach ($defaultRenderProperties as $propName => $propVal) {
            $requestObject->setRenderProperty($propName, $propVal);
        }

        /**
         * Dispatch the request.
         */
        $this->dispatch($requestObject);

        /**
         * Render the request.
         */
        $this->render($requestObject);
    }

    /**
     * Dispatch (handle) a request.
     *
     * Dispatch resolves the request context, loads the context data,
     * imports it into the request object and executes associated logic.
     *
     * @param IRequestAdapter $cro
     */
    public function dispatch(IRequestAdapter &$cro): void {
        Logger::debug('Dispatching');

        Logger::debug('Issuing context helpers');

        /**
         * Get Request Context
         */
        $contextKey = $cro->getRequestContext();

        Logger::debug('Resolving context: [%s]', $contextKey);

        $contextData = $this->helpers['context']->resolveContext($contextKey);
        $contextData['key'] = $contextKey;

        Logger::debug('Loading context into Request object...');

        /**
         * Load the Context into the Request object
         */
        $cro->loadContext($contextData);

        Logger::debug('Get Logic calls from the Request object...');

        /**
         * If $cro has no valid logic, give a fatal error
         */
        if (!is_array($cro->getLogic())) {
            Logger::fatal('Dispatch/Error in getLogic result');
        }

        Logger::debug('Executing Logic calls from the Request object [%d]...', count($cro->getLogic()));

        /**
         * Iterate over request logic.
         */
        while ($call = $cro->getNextLogic()) {
            /**
             * Check if we're still clear to run
             */
            if ($cro->hasException()) {
                break;
            }

            /**
             * If getNextLogic generated an error, then bail.
             */
            if (!$call || !isset($call['class']) || !isset($call['method'])) {
                break;
            }

            Logger::debug('Executing Logic call: [%s->%s]', $call['class'], $call['method']);

            /**
             * Get a (cached) instance of the defined logic class.
             */
            $logicObject = $this->getCachedObject($call['class']);

            /**
             * Aggregate the logic properties (class and method) in callObj for execution.
             */
            $callObj = [$logicObject, $call['method']];

            /**
             * If the method does not exist, then bail, else execute the class method.
             * If the class method returns false, then stop further execution (of logic).
             */
            if (!method_exists($logicObject, $call['method'])) {
                $cro->doException();
                Logger::fatal('Fatal Error In Request');
            } else {
                if (Logger::shouldDebug()) {
                    $res = call_user_func_array($callObj, [&$cro]);
                } else {
                    $res = @call_user_func_array($callObj, [&$cro]);
                }

                if ($res === false) {
                    $cro->doException();
                }

                Logger::debug('Logic Call [%s->%s] returned: [%s]', $call['class'], $call['method'], (string) $res);
            }
        }

        Logger::debug('Done executing Logic calls');
    }

    /**
     * This method is responsible for rendering the request.
     *
     * @param IRequestAdapter $cro
     */
    public function render(IRequestAdapter &$cro): void {
        /**
         * Get the render engine as set in the request.
         */
        $renderEngine = $cro->getRenderEngine();

        Logger::debug('Render with: [%s]', $renderEngine);

        /**
         * Get a (cached) instance of the render engine.
         */
        $renderInstance = $this->getCachedObject($renderEngine);

        Logger::debug('Rendering...');

        /**
         * Dispatch the render request to the rendering engine.
         */
        $renderInstance->dispatch($cro);
    }

    /**
     * Returns a cached instance of the class name type.
     * If it does not exist, it creates an instance of the class name and saves it into the private objectCache array.
     *
     * @param string $className
     *
     * @return object
     */
    public function getCachedObject($className) {
        Logger::debug('getCachedObject: [%s]', $className);

        /**
         * Check if the class is defined.
         */
        if (!class_exists($className)) {
            Logger::fatal('getCachedObject/Missing class: [%s]', $className);
        }

        /**
         * If the object is not cached, create the object and save it in the cache.
         */
        if (!isset($this->objectCache[$className])) {
            $this->objectCache[$className] = new $className();
        }

        /**
         * Check if the cached object matched the specified class name. Bail on failure.
         */
        if (!is_a($this->objectCache[$className], $className)) {
            Logger::fatal('getCachedObject/Could not instantiate class: [%s]', $className);
        }

        /**
         * Return the object from the cache.
         */
        return $this->objectCache[$className];
    }

    /**
     * Factorize a class by class name and confirm it matches the class interface.
     * Returns an instance of the class name if successful.
     *
     * @param string $className
     * @param string $classInterface
     *
     * @return object
     */
    public function factory($className, $classInterface, ...$args) {
        Logger::debug('making %s', $className);

        /**
         * Check if the class is defined.
         */
        if (!class_exists($className)) {
            Logger::fatal('Missing class: [%s]', $className);
        }

        /**
         * Create a new instance of the specified class name.
         */
        $objectClass = is_array($args) ? new $className(...$args) : new $className($args);

        /**
         * If the created instance does not implement the specified class interface, then bail.
         */
        if (!is_a($objectClass, $classInterface)) {
            Logger::fatal('Class is not of interface: [%s]', $classInterface);
        }

        /**
         * Return the created instance.
         */
        return $objectClass;
    }
}
