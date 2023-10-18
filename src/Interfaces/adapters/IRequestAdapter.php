<?php

namespace Colore\Interfaces\Adapters;

interface IRequestAdapterMagicAspects {
    public function __get($requestVariable);

    public function __set($requestVariable, $requestValue);

    public function __isset($requestVariable);

    public function __unset($requestVariable);
}

interface IRequestAdapterRequestAspects {


    public function getRequestArgument($requestArgumentName);
}

interface IRequestAdapterRenderAspects {


    /**
     * Get a specific render argument.
     *
     * @param string $renderArgumentName
     *
     * @return mixed
     */
    public function getRenderArgument($renderArgumentName);

    /**
     * Get the render path. This is the identifier for the template the renderer uses to render the request.
     *
     * @return string
     */
    public function getRenderPath();

    /**
     * Get the rendering engine for the current request.
     *
     * @return string Returns a string with the currently set rendering engine.
     */
    public function getRenderEngine();

    /**
     * Output
     *
     * @param mixed Output variable
     */
    public function output($content, $metadata = [], $status = 0);

    public function getRenderProperties();

    public function getRenderProperty($renderProperty);

    public function setRenderProperty($renderProperty, $renderValue);
}

interface IRequestAdapterSessionAspects {

}

interface IRequestAdapterLogicAspects {
    public function hasException();

    public function doException();

    public function getLogic();

    public function getNextLogic();
}

interface IRequestAdapterContextAspects {
    public function getContextKey();

    public function loadContext($contextData);
}

interface IRequestAdapter extends
    IRequestAdapterMagicAspects,
    IRequestAdapterRequestAspects,
    IRequestAdapterRenderAspects,
    IRequestAdapterSessionAspects,
    IRequestAdapterLogicAspects,
    IRequestAdapterContextAspects {
}
