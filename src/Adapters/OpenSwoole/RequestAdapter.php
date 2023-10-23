<?php

namespace Colore\Adapters\OpenSwoole;

use Colore\GenericRequestAdapter;
use Colore\Logger;
use Colore\Interfaces\Adapters\IRequestAdapter;

use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use Colore\Providers\InMemory\SimpleSessionProvider;

class RequestAdapter extends GenericRequestAdapter implements IRequestAdapter {
    protected $request_properties = [];

    private Request $request;
    private Response $response;

    public function __construct(Request &$request, Response &$response) {
        $this->request = $request;
        $this->response = $response;

        $sessionCookie = null;

        if (isset($this->request->cookie['COLORE_OPENSWOOLE_SESSION_ID'])) {
            $sessionCookie = $this->request->cookie['COLORE_OPENSWOOLE_SESSION_ID'];
        }

        $this->session = SimpleSessionProvider::getSession($sessionCookie);

        $this->response->cookie(
            'COLORE_OPENSWOOLE_SESSION_ID',
            $this->session->getSessionId(),
            time() + 1800,
            '/',
            $request->header['host'],
            false,
            true
        );
    }

    public function getContextKey() {
        $context = $this->request->server['request_uri'];

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
        return $this->request->get;
    }

    /**
     * Get a specific request argument. Returns null if the specified request argument does not exist.
     * @param string $requestArgumentName
     * @return multitype:|NULL
     */
    public function getRequestArgument($requestArgumentName) {
        if (isset($this->request->get[$requestArgumentName])) {
            return $this->request->get[$requestArgumentName];
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
        return $this->request->post;
    }

    /**
     * Get a specific request property. Returns null if the specified request property does not exist.
     * @param string $requestProperty
     * @return multitype:|NULL
     */
    public function getRequestProperty($requestProperty) {
        if (isset($this->request->post[$requestProperty])) {
            return $this->request->post[$requestProperty];
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
     */
    public function getSessionProperties() {
        return $this->session;
    }

    /**
     * Sets a session lifetime.
     *
     * @param integer $sessionLifetime
     */
    public function setSessionLifetime($sessionLifetime = 1800): void {
        $this->response->cookie(
            'COLORE_OPENSWOOLE_SESSION_ID',
            $this->session->getSessionId(),
            time() + $sessionLifetime,
            '/',
            $this->request->header['host'],
            false,
            true
        );
    }

    /**
     * Get a (named) session property. Returns null or the session property if it exists.
     * @param unknown $sessionProperty
     * @return multitype:|NULL
     */
    public function getSessionProperty($sessionProperty) {
        if (isset($this->session[$sessionProperty])) {
            return $this->session[$sessionProperty];
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
        $this->session[$sessionProperty] = $sessionValue;
    }

    /**
     * Sets a session property.
     *
     * @param string $sessionProperty
     *
     * @return void
     */
    public function unsetSessionProperty($sessionProperty) {
        if (isset($this->session[$sessionProperty])) {
            unset($this->session[$sessionProperty]);
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
        $this->response->status($status);

        foreach ($metadata as $headerName => $headerValue) {
            $this->response->header($headerName, $headerValue);
        }

        $this->response->end($content);
    }
}
