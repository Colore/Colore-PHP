<?php

class Render_JSON implements ColoreRenderHelper {

	private $_defaults = array();

	public function Dispatch( ColoreRequestHelper &$cro ) {

		global $config;

		$this->_defaults = $config['defaults']['render'];

		$outputProperties = $this->_defaults['properties'];

		$renderProperties = $cro->getRenderProperties();

		if( LOGLEVEL & LOG_DEBUG )
			@error_log( sprintf( "%s: Setting render properties [%d]", __METHOD__, count( $renderProperties ) ) );
		
		while( list( $propName, $propVal ) = each( $renderProperties ) ) {
			if( LOGLEVEL & LOG_DEBUG ) @error_log( sprintf( "%s: Setting render property [%s] to [%s]", __METHOD__, $propName, $propVal ) );
			$outputProperties[$propName] = $propVal;
		}

		header( 'Content-Type: application/json' );
		
		json_encode( $outputProperties );

	}

}

?>
