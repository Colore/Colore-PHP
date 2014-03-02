<?php

class Render_Simple implements ColoreRenderHelper {

	private $_defaults = array();

	public function Dispatch( ColoreRequestHelper &$cro ) {
		global $config;

		$defaults = $config['defaults']['render'];

		$template = $cro->getRenderPath();
		$template_file = sprintf( "%s/templates/%s", BASEDIR, $template );

		$defaultProperties = $defaults['properties'];

		while( list( $propName, $propVal ) = each( $defaultProperties ) ) {
			$_GLOBALS[$propName] = $propVal;
		}

		$renderProperties = $cro->getRenderProperties();

		if( LOGLEVEL & LOG_DEBUG )
			@error_log( sprintf( "%s: Setting render properties [%d]", __METHOD__, count( $renderProperties ) ) );
		while( list( $propName, $propVal ) = each( $renderProperties ) ) {
			if( LOGLEVEL & LOG_DEBUG )
				@error_log( sprintf( "%s: Setting render property [%s] to [%s]", __METHOD__, $propName, $propVal ) );
			$_GLOBALS[$propName] = $propVal;
		}

		require_once( $template_file );
	}

}

?>
