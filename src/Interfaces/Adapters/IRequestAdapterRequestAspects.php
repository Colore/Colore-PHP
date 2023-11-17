<?php

namespace Colore\Interfaces\Adapters;

interface IRequestAdapterRequestAspects {
    public function getRequestArguments();
    public function getRequestArgument($requestArgumentName);
    public function setRequestArgument($requestArgumentName, $requestArgumentValue);

    public function getContextRenderProperties();

    public function getRequestProperties();
    public function getRequestProperty($requestPropertyName);
    public function setRequestProperty($requestPropertyName, $requestPropertyValue);
}
