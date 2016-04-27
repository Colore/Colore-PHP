<?php

// Update include path to include current path
set_include_path( get_include_path() . PATH_SEPARATOR . dirname ( __FILE__ ) );

/*
 * Include custom classes/files here.
 */

// Include genpassword.php
require_once( "genpassword.php" );

// Include MySQLUTCTimestamp.php
require_once( "MySQLUTCTimestamp.php" );

// Include ValidateEmail.php
require_once( "ValidateEmail.php" );

?>
