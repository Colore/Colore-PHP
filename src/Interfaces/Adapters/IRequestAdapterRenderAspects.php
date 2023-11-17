<?php

namespace Colore\Interfaces\Adapters;

interface IRequestAdapterRenderAspects {
    /**
     * Get all render arguments.
     *
     * @return mixed
     */
    public function getRenderArguments();

    /**
     * Get a specific render argument.
     *
     * @param string $renderArgumentName
     *
     * @return mixed
     */
    public function getRenderArgument($renderArgumentName);

    /**
     * Set a specific render argument.
     *
     * @param string $renderArgumentName
     * @param mixed $renderArgumentValue
     *
     * @return void
     */
    public function setRenderArgument($renderArgumentName, $renderArgumentValue);

    /**
     * Get all render properties.
     *
     * @return mixed
     */
    public function getRenderProperties();

    /**
     * Get a specific render property.
     *
     * @param string $renderProperty
     *
     * @return mixed
     */
    public function getRenderProperty($renderProperty);

    /**
     * Set a specific render property.
     *
     * @param string $renderProperty
     * @param mixed $renderValue
     *
     * @return void
     */
    public function setRenderProperty($renderProperty, $renderValue);

    /**
     * Get the rendering engine for the current request.
     *
     * @return string Returns a string with the currently set rendering engine.
     */
    public function getRenderEngine();

    /**
     * Set the render engine for the current request.
     *
     * @param string $renderEngine
     *
     * @return void
     */
    public function setRenderEngine($renderEngine);

    /**
     * Get the render path. This is the identifier for the template the renderer uses to render the request.
     *
     * @return string
     */
    public function getRenderPath();

    /**
     * Set render (template) path
     *
     * @param [type] $renderPath
     * @return void
     */
    public function setRenderPath($renderPath);

    /**
     * Output
     *
     * @param mixed Output variable
     */
    public function output($content, $metadata = [], $status = 0);
}
