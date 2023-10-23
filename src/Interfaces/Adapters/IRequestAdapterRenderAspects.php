<?php

namespace Colore\Interfaces\Adapters;

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
