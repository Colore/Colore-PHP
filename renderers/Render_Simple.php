<?php

class Render_Simple implements ColoreRenderHelper {

	private $_defaults = array();

	public function __construct() {
	}

	public function Dispatch( ColoreRequestHelper &$cro ) {
		global $config;

		$this->_defaults = $config['defaults']['render'];

		$smarty = new Smarty();

		$template = $cro->getRenderPath();

		$smarty->clearAllAssign();

		$defaultProperties = $this->_defaults['properties'];

		while( list( $propName, $propVal ) = each( $defaultProperties ) ) {
			$smarty->assign( $propName, $propVal );
		}

		$renderProperties = $cro->getRenderProperties();

		if( LOGLEVEL & LOG_DEBUG )
			@error_log( sprintf( "%s: Setting render properties [%d]", __METHOD__, count( $renderProperties ) ) );
		while( list( $propName, $propVal ) = each( $renderProperties ) ) {
			if( LOGLEVEL & LOG_DEBUG )
				@error_log( sprintf( "%s: Setting render property [%s] to [%s]", __METHOD__, $propName, $propVal ) );
			$smarty->assign( $propName, $propVal );
		}

		$smarty->display( $template );

	}

}

?>
