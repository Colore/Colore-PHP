<?php

namespace Colore;

class Request {
    protected $exceptionState = false;
    protected $properties;
    protected $settings;
    protected $contextKey = '';
    protected $context = [];
    protected $requestArguments = [];
    protected $requestProperties = [];
    protected $requestVariables = [];
    protected $sessionProperties = [];
    protected $renderProperties = [];

    /**
     * Magic overload getter. Returns the requestVariable value or null.
     * @param string $requestVariable
     * @return mixed
     */
    public function __get($requestVariable) {
        if (array_key_exists($requestVariable, $this->requestVariables)) {
            return $this->requestVariables[$requestVariable];
        }

        return null;
    }

    /**
     * Magic overload setter
     * @param string $requestVariable
     * @param string $requestValue
     */
    public function __set($requestVariable, $requestValue) {
        $this->requestVariables[$requestVariable] = $requestValue;
    }

    /**
     * Magic overload checker to determine if the variable is set.
     * @param unknown $requestVariable
     * @return boolean
     */
    public function __isset($requestVariable) {
        return array_key_exists($requestVariable, $this->requestVariables);
    }

    /**
     * Magical unsetter for request variables.
     * @param unknown $requestVariable
     */
    public function __unset($requestVariable) {
        unset($this->requestVariables[$requestVariable]);
    }

    /**
     * Returns an array containing all of the request arguments.
     * @return array
     */
    public function getRequestArguments() {
        return $this->requestArguments;
    }

    /**
     * Get a specific request argument. Returns null if the specified request argument does not exist.
     * @param string $requestArgumentName
     * @return multitype:|NULL
     */
    public function getRequestArgument($requestArgumentName) {
        if (isset($this->requestArguments[$requestArgumentName])) {
            return $this->requestArguments[$requestArgumentName];
        }

        return null;
    }

    /**
     * Sets a request argument.
     * @param string $requestArgument
     * @param mixed $requestArgumentValue
     */
    public function setRequestArgument($requestArgument, $requestArgumentValue) {
        $this->requestArguments[$requestArgument] = $requestArgumentValue;
    }

    /**
     * Returns an array containing all of the request properties.
     * @return array
     */
    public function getRequestProperties() {
        return $this->requestProperties;
    }

    /**
     * Get a specific request property. Returns null if the specified request property does not exist.
     * @param string $requestProperty
     * @return multitype:|NULL
     */
    public function getRequestProperty($requestProperty) {
        if (isset($this->requestProperties[$requestProperty])) {
            return $this->requestProperties[$requestProperty];
        }

        return null;
    }

    /**
     * Sets a request property.
     * @param string $requestProperty
     * @param string $requestValue
     */
    public function setRequestProperty($requestProperty, $requestValue) {
        $this->requestProperties[$requestProperty] = $requestValue;
    }

    /**
     * Get the context's rendering properties.
     * @return array Returns an array with all the rendering properties.
     */
    public function getContextRenderProperties() {
        return $this->context['render']['properties'];
    }

    /**
     * Gets an array containing all of the rendering properties.
     * @return array
     */
    public function getRenderProperties() {
        return $this->renderProperties;
    }

    /**
     * Get a (named) render property. Returns null or the render property if it exists.
     * @param string $renderProperty
     * @return multitype:|NULL
     */
    public function getRenderProperty($renderProperty) {
        if (isset($this->renderProperties[$renderProperty])) {
            return $this->renderProperties[$renderProperty];
        }

        return null;
    }

    /**
     * Set a render property
     * @param string $renderProperty
     * @param mixed $renderValue
     */
    public function setRenderProperty($renderProperty, $renderValue) {
        Logger::debug('Set [%s] to [%s]', $renderProperty, $renderValue);

        $this->renderProperties[$renderProperty] = $renderValue;
    }

    /**
     * Returns an array containing all of the session properties.
     * @return array
     */
    public function getSessionProperties() {
        return $this->sessionProperties;
    }

    /**
     * Get a (named) session property. Returns null or the session property if it exists.
     * @param unknown $sessionProperty
     * @return multitype:|NULL
     */
    public function getSessionProperty($sessionProperty) {
        if (isset($this->sessionProperties[$sessionProperty])) {
            return $this->sessionProperties[$sessionProperty];
        }

        return null;
    }

    /**
     * Sets a session property.
     * @param string $sessionProperty
     * @param mixed $sessionValue
     */
    public function setSessionProperty($sessionProperty, $sessionValue) {
        $this->sessionProperties[$sessionProperty] = $sessionValue;
    }

    /**
     * Sets a session property.
     * @param string $sessionProperty
     */
    public function unsetSessionProperty($sessionProperty) {
        if (isset($this->sessionProperties[$sessionProperty])) {
            unset($this->sessionProperties[$sessionProperty]);
        }
    }

    /**
     * Get the key of the current context
     * @return Returns the current context key
     */
    public function getContextKey() {
        return $this->contextKey;
    }

    /**
     *
     * @param array $contextData
     */
    public function loadContext($contextData) {
        // Save context key
        $this->contextKey = $contextData['key'];

        // Load context information
        $this->context = $contextData;

        $this->exceptionState = false;

        if (
            isset($this->context['render']['properties']) &&
            is_array($this->context['render']['properties']) &&
            count($this->context['render']['properties']) > 0
        ) {
            Logger::debug('We have render arguments');

            reset($this->context['render']['properties']);

            foreach ($this->context['render']['properties'] as $renderProperty => $renderValue) {
                Logger::debug('Set [%s] to [%s]', $renderProperty, (string) $renderValue);

                $this->setRenderProperty($renderProperty, $renderValue);
            }

            reset($this->context['render']['properties']);
        } else {
            Logger::debug('No render arguments');
        }
    }

    public function hasException() {
        return $this->exceptionState;
    }

    public function doException() {
        $this->exceptionState = true;
    }

    /**
     * Get all the logic for the request.
     * @return array Returns an array
     */
    public function getLogic() {
        if (!isset($this->context['logic']) || !is_array($this->context['logic'])) {
            return [];
        }

        return $this->context['logic'];
    }

    /**
     * Gets the next Logic element from the stack.
     * @return mixed Logic element
     */
    public function getNextLogic() {
        // If we have a non-empty preempt_logic list, merge it into the logic list
        if (isset($this->context['preempt_logic']) && count($this->context['preempt_logic'])) {
            while (count($this->context['preempt_logic'])) {
                array_unshift($this->context['logic'], array_pop($this->context['preempt_logic']));
            }
        }

        // Return the next logic call from the stack
        if (count($this->context['logic']) > 0) {
            return array_shift($this->context['logic']);
        }
        return false;
    }

    /**
     * Append logic to the worklist
     * @param array Logic
     */
    public function appendLogic(array $logic) {
        $this->context['logic'][] = $logic;
    }

    /**
     * Insert logic to the worklist
     * @param array Logic
     */
    public function insertLogic(array $logic) {
        // Check for preempt list
        if (!isset($this->context['preempt_logic'])) {
            $this->context['preempt_logic'] = [];
        }

        // Add logic to preempt list
        if (isset($logic['class']) && isset($logic['method'])) {
            array_push($this->context['preempt_logic'], $logic);
        }
    }

    /**
     * Get the rendering engine for the current request.
     *
     * @return string Returns a string with the currently set rendering engine.
     */
    public function getRenderEngine() {
        return $this->context['render']['engine'];
    }

    /**
     * Set the rendering engine for the current request.
     * @param string $renderEngine
     */
    public function setRenderEngine($renderEngine) {
        $this->context['render']['engine'] = $renderEngine;
    }

    /**
     * Get rendering path.
     * @return string Returns the render path if set, else returns false.
     */
    public function getRenderPath() {
        if (isset($this->context['render']['path'])) {
            return $this->context['render']['path'];
        }
        return false;
    }

    /**
     * Sets the render path
     * @param string $renderPath
     */
    public function setRenderPath($renderPath) {
        $this->context['render']['path'] = $renderPath;
    }

    /**
     * Deal with new PHP BS by wrapping this in an object to we can pass by reference.
     *
     * @returns RequestWrapper
     */
    public function getWrappedObject() {
        return [&$this];
    }
}
