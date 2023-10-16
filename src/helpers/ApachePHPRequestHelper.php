<?php

namespace Colore\Helpers;

use Colore\Request;
use Colore\Logger;
use Colore\Interfaces\RequestHelper;

@session_start();

class ApachePHPRequestHelper extends Request implements RequestHelper {
    protected $request_properties = [];

    public function __construct() {
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2) {
                $this->request_properties[$keyval[0]] = urldecode($keyval[1]);
            }
        }
    }

    public function getContextKey() {
        $baseURL = dirname($_SERVER['SCRIPT_NAME']);

        Logger::trace('baseUrl: %s', $baseURL);

        if (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
            $context = $_SERVER['PATH_INFO'];
            Logger::trace('PATH_INFO -> %s', $context);
        } elseif (isset($_SERVER['REDIRECT_URL']) && !empty($_SERVER['REDIRECT_URL'])) {
            $context = $_SERVER['REDIRECT_URL'];
            Logger::trace('REDIRECT_URL -> %s', $context);
        } else {
            $context = $_SERVER['REQUEST_URI'];
            Logger::trace('REQUEST_URI -> %s', $context);
        }

        if ($context === '/') {
            $context = '';
        }

        Logger::debug('Context: [%s]', $context);

        return $context;
    }

    /**
     * Returns an array containing all of the request arguments.
     * @return array
     */
    public function getRequestArguments() {
        return $_GET;
    }

    /**
     * Get a specific request argument. Returns null if the specified request argument does not exist.
     * @param string $requestArgumentName
     * @return multitype:|NULL
     */
    public function getRequestArgument($requestArgumentName) {
        if (isset($_GET[$requestArgumentName])) {
            return $_GET[$requestArgumentName];
        }

        return null;
    }

    /**
     * Sets a request argument.
     * @param string $requestArgument
     * @param mixed $requestArgumentValue
     */
    public function setRequestArgument($requestArgument, $requestArgumentValue) {
        /**
         * We don't want to inject data into the _GET variable.
         */
    }

    /**
     * Returns an array containing all of the request properties.
     * @return array
     */
    public function getRequestProperties() {
        return $_POST;
    }

    /**
     * Get a specific request property. Returns null if the specified request property does not exist.
     * @param string $requestProperty
     * @return multitype:|NULL
     */
    public function getRequestProperty($requestProperty) {
        if (isset($_POST[$requestProperty])) {
            return $_POST[$requestProperty];
        }

        return null;
    }

    /**
     * Sets a request property.
     * @param string $requestProperty
     * @param mixed $requestValue
     */
    public function setRequestProperty($requestProperty, $requestValue) {
        /**
         * We don't want to inject data into the _POST variable.
         */
    }

    /**
     * Returns an array containing all of the session properties.
     * @return array
     */
    public function getSessionProperties() {
        return $_SESSION;
    }

    /**
     * Sets a session lifetime.
     * @param integer $sessionLifetime
     */
    public function setSessionLifetime($sessionLifetime = 1800) {
        session_set_cookie_params($sessionLifetime);
    }

    /**
     * Get a (named) session property. Returns null or the session property if it exists.
     * @param unknown $sessionProperty
     * @return multitype:|NULL
     */
    public function getSessionProperty($sessionProperty) {
        if (isset($_SESSION[$sessionProperty])) {
            return $_SESSION[$sessionProperty];
        }

        return null;
    }

    /**
     * Sets a session property.
     * @param string $sessionProperty
     * @param mixed $sessionValue
     */
    public function setSessionProperty($sessionProperty, $sessionValue) {
        $_SESSION[$sessionProperty] = $sessionValue;
    }

    /**
     * Sets a session property.
     * @param string $sessionProperty
     */
    public function unsetSessionProperty($sessionProperty) {
        if (isset($_SESSION[$sessionProperty])) {
            unset($_SESSION[$sessionProperty]);
        }
    }
}
