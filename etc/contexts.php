<?php

$config['contexts'] = array();

$config['contexts']['default'] = array(
	'properties' => array(
	),
	'logic' => array(
	),
	'render' => array(
		'engine' => 'Render_Simple',
		'path' => 'home.php',
		'properties' => array(
		),
	),
);

$config['contexts']['error'] = array(
	'properties' => array(
	),
	'logic' => array(
	),
	'render' => array(
		'engine' => 'Render_Smarty',
		'path' => 'error.php',
		'properties' => array(
		),
	),
);

?>
