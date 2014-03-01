<?php

interface ColoreRequestHelper {
	public function __get( string $requestVariable );
	public function __set( string $requestVariable, $requestValue );
	public function __isset( string $requestVariable );
	public function __unset( string $requestVariable );
	public function getRequestArguments();
	public function getRequestArgument( string $requestArgumentName );
	public function setRequestArgument( string $requestArgument, $requestArgumentValue );
	public function getRequestProperties();
	public function getRequestProperty( string $requestProperty );
	public function setRequestProperty( string $requestProperty, $requestValue );
	public function getRequestVariable( string $requestVariable );
	public function setRequestVariable( string $requestVariable, $requestValue );
	public function getRenderProperties();
	public function getRenderProperty( string $renderProperty );
	public function setRenderProperty( string $renderProperty, $renderValue );
	public function getSessionProperties();
	public function getSessionProperty( string $sessionProperty );
	public function setSessionProperty( string $sessionProperty, $sessionValue );
	public function unsetSessionProperty( string $sessionProperty );
	public function getContext();
	public function loadContext( $contextName );
	public function hasException();
	public function doException();
	public function getLogic();
	public function getNextLogic();
	public function appendLogic( array $logic );
	public function insertLogic( array $logic );
	public function getRenderEngine();
	public function setRenderEngine( string $renderEngine );
	public function getRenderArguments();
	public function getRenderPath();
	public function setRenderPath( string $renderPath );
}