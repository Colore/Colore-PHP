<?php

// Colore settings
$config['colore'] = array();

// Defaults
$config['colore']['defaults'] = array();

// Contexts
$config['colore']['defaults']['contexts'] = array(
	'default' => 'default',
	'error' => 'error',
);

// Render defaults
$config['colore']['defaults']['render'] = array();
$config['colore']['defaults']['render']['properties'] = array();

// Helpers
$config['colore']['helpers'] = array(
	'context' => 'ColorePHPContextHelper',
	'request' => 'ColoreApachePHPRequestHelper',
);

?>