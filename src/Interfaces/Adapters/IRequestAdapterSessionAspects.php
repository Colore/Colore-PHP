<?php

namespace Colore\Interfaces\Adapters;

interface IRequestAdapterSessionAspects {
    /**
     * Returns an array containing all of the session properties.
     *
     * @return array
     */
    public function getSessionProperties();

    /**
     * Get a (named) session property. Returns null or the session property if it exists.
     *
     * @param unknown $sessionProperty
     *
     * @return multitype:|NULL
     */
    public function getSessionProperty($sessionProperty);

    /**
     * Sets a session property.
     *
     * @param string $sessionProperty
     * @param mixed $sessionValue
     *
     * @return void
     */
    public function setSessionProperty($sessionProperty, $sessionValue);

    /**
     * Sets a session property.
     *
     * @param string $sessionProperty
     *
     * @return void
     */
    public function unsetSessionProperty($sessionProperty);
}
