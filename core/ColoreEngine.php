<?php

class ColoreEngine {

	private $_objectCache = array();
	private $_config = array();
	private $_helpers = array();

	public function __construct( array $coloreConfig ) {
		global $config;
		
		if( ! isset( $config['colore'] ) )
			die( sprintf( "%s: Error initializing Colore configuration", __METHOD__ ) );
		
		$this->_config = $config['colore'];
		
	}
	
	public function Service() {
		
		$contextHelper = $this->Factory( $this->_config['helpers']['context'], 'ColoreContextHelper' );
		
		if( ! $contextHelper )
			$this->Fatal( "Failed to acquire context helper" );
		
		$this->_helpers['context'] = $contextHelper;
		
		$requestObject = $this->Factory( $this->_config['helpers']['request'], 'ColoreRequestHelper' );
		
		if( ! $requestObject )
			$this->Fatal( "Failed to acquire request helper" );
		
		$this->Dispatch( $requestObject );
		
		$this->Render( $requestObject );
		
	}

	public function Dispatch( ColoreRequestHelper &$cro ) {

		if( LOGLEVEL & LOG_DEBUG ) error_log( sprintf( "%s: %s", __METHOD__, "Dispatching" ) );

		if( LOGLEVEL & LOG_DEBUG ) error_log( sprintf( "%s: %s", __METHOD__, "Issuing context helpers" ) );

		/**
		 * Get Request Context
		 */
		$contextName = $cro->getContext();

		if( LOGLEVEL & LOG_DEBUG ) error_log( sprintf( "%s: %s", __METHOD__, "Resolving context..." ) );
		
		$contextData = $this->_helpers['context']->getContext( $contextName );
		$contextData['name'] = $contextName;
		
		if( LOGLEVEL & LOG_DEBUG ) error_log( sprintf( "%s: %s", __METHOD__, "Loading context into Request object..." ) );
		
		/**
		 * Load the Context into the Request object
		 */
		$cro->loadContext( $contextData );

		if( LOGLEVEL & LOG_DEBUG ) error_log( sprintf( "%s: %s", __METHOD__, "Get Logic calls from the Request object..." ) );
		
		/**
		 * If $cro has no valid logic, give a fatal error
		 */ 
		if( ! $cro->getLogic() )
			$this->Fatal( "Dispatch/Error in getLogic result" );

		if( LOGLEVEL & LOG_DEBUG ) error_log( sprintf( "%s: Executing Logic calls from the Request object [%d]...", __METHOD__, count( $cro->getLogic() ) ) );

		while( $call = $cro->getNextLogic() )
		{
			// Check if we're still clear to run
			if( $cro->hasException() )
				break;
			
			// If getNextLogic generated an error, then bail
			if( ! $call || ! isset( $call['class'] ) || ! isset( $call['method'] ) )
				break;

			if( LOGLEVEL & LOG_DEBUG ) error_log( sprintf( "%s: Executing Logic call: [%s->%s]", __METHOD__, $call['class'], $call['method'] ) );

			$logicObject = $this->getCachedObject( $call['class'] );

			$callObj = array(
					$logicObject,
					$call['method'],
			);

			if( ! method_exists( $logicObject, $call['method'] ) ) {
				$cro->doException();
				$this->Fatal( "Fatal Error In Request" );
			} else {
				if( LOGLEVEL & LOG_DEBUG ) {
					$res = call_user_func( $callObj, $cro );
				} else {
					$res = @call_user_func( $callObj, $cro );
				}
	
				if( $res === false )
					$cro->doException();
	
				if( LOGLEVEL & LOG_DEBUG ) error_log( sprintf( "%s: Logic Call [%s->%s] returned: [%s]", __METHOD__, $call['class'], $call['method'], (string) $res ) );
			}
		}

		if( LOGLEVEL & LOG_DEBUG ) error_log( sprintf( "%s: %s", __METHOD__, "Done executing Logic calls" ) );

	}
	
	public function Render( ColoreRequestWorker &$cro ) {

		$renderEngine = $cro->getRenderEngine();

		if( LOGLEVEL & LOG_DEBUG ) error_log( sprintf( "%s: Render with: [%s]", __METHOD__, $renderEngine ) );

		$renderInstance = $this->getCachedObject( $renderEngine );

		if( LOGLEVEL & LOG_DEBUG ) error_log( sprintf( "%s: %s", __METHOD__, "Rendering..." ) );

		$renderInstance->Dispatch( $cro );

	}

	public function getCachedObject( $className ) {

		if( LOGLEVEL & LOG_DEBUG )
			error_log( sprintf( "%s: getCachedObject: [%s]", __METHOD__, $className ) );

		if( ! class_exists( $className ) )
			$this->Fatal( sprintf( "%s->%s: getCachedObject/Missing class: [%s]", __METHOD__, $className ) );

		if( ! isset( $this->_objectCache[$className] ) )
			$this->_objectCache[$className] = new $className;

		if( ! is_a( $this->_objectCache[$className], $className ) )
			$this->Fatal( sprintf( "%s->%s: getCachedObject/Could not instantiate class: [%s]", __METHOD__, $className ) );

		return $this->_objectCache[$className];

	}

	public function Factory( string $className, string $classInterface ) {

		if( LOGLEVEL & LOG_DEBUG )
			error_log( sprintf( "%s: [%s]", __METHOD__, $className ) );

		if( ! class_exists( $className ) )
			$this->Fatal( sprintf( "%s: Missing class: [%s]", __METHOD__, $className ) );

		$objectClass = new $className;

		if( ! is_a( $objectClass, $className ) )
			$this->Fatal( sprintf( "%s: Could not instantiate class: [%s]", __METHOD__, $className ) );

		if( ! is_a( $objectClass, $classInterface ) )
			$this->Fatal( sprintf( "%s: Class is not of interface: [%s]", __METHOD__, $classInterface ) );

		return $objectClass;

	}

	public function Fatal( $errorMessage ) {

		error_log( sprintf( "%s: %s", __METHOD__, $errorMessage ) );

		die( "A fatal error occured." );

	}

}

?>
