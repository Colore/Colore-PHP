<?php

namespace Colore\Adapters\Default;

use Colore\GenericRequestAdapter;
use Colore\Logger;
use Colore\Interfaces\Adapters\IRequestAdapter;

@session_start();

class RequestAdapter extends GenericRequestAdapter implements IRequestAdapter {
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

    public function getRequestContext(): string {
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
     *
     * @param string $requestArgumentName
     *
     * @return (array|string)[]|null|string
     *
     * @psalm-return array<int|string, array<int|string, mixed>|string>|null|string
     */
    public function getRequestArgument($requestArgumentName) {
        if (isset($_GET[$requestArgumentName])) {
            return $_GET[$requestArgumentName];
        }

        if (isset($this->requestArguments[$requestArgumentName])) {
            return $this->requestArguments[$requestArgumentName];
        }

        return null;
    }

    /**
     * Sets a request argument.
     *
     * @param string $requestArgument
     * @param mixed $requestArgumentValue
     *
     * @return void
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
     *
     * @param string $requestProperty
     *
     * @return (array|string)[]|null|string
     *
     * @psalm-return array<int|string, array<int|string, mixed>|string>|null|string
     */
    public function getRequestProperty($requestProperty) {
        if (isset($_POST[$requestProperty])) {
            return $_POST[$requestProperty];
        }

        return null;
    }

    /**
     * Sets a request property.
     *
     * @param string $requestProperty
     * @param mixed $requestValue
     *
     * @return void
     */
    public function setRequestProperty($requestProperty, $requestValue) {
        /**
         * We don't want to inject data into the _POST variable.
         */
    }

    /**
     * Returns an array containing all of the session properties.
     *
     * @return array
     *
     * @psalm-return array<string, mixed>
     */
    public function getSessionProperties(): array {
        return $_SESSION;
    }

    /**
     * Sets a session lifetime.
     *
     * @param integer $sessionLifetime
     */
    public function setSessionLifetime($sessionLifetime = 1800): void {
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
     *
     * @param string $sessionProperty
     * @param mixed $sessionValue
     *
     * @return void
     */
    public function setSessionProperty($sessionProperty, $sessionValue) {
        $_SESSION[$sessionProperty] = $sessionValue;
    }

    /**
     * Sets a session property.
     *
     * @param string $sessionProperty
     *
     * @return void
     */
    public function unsetSessionProperty($sessionProperty) {
        if (isset($_SESSION[$sessionProperty])) {
            unset($_SESSION[$sessionProperty]);
        }
    }

    /**
     * Output
     *
     * @param mixed Output variable
     *
     * @return void
     */
    public function output($content, $metadata = [], $status = 200) {
        http_response_code($status);

        foreach ($metadata as $headerName => $headerValue) {
            header(sprintf('%s: %s', $headerName, $headerValue));
        }

        echo $content;
    }
}
