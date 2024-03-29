<?php

define('BASEDIR', __DIR__);

require_once __DIR__ . '/../../vendor/autoload.php';

use Colore\Logger;
use Colore\Engine;

Logger::setLogLevel(LOG_TRACE);
Logger::setBasePath(__DIR__);

$config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);

/**
 * Create a new ColoreEngine instance.
 */
Logger::debug('Instantiating ColoreEngine');

$colore = new Engine($config);

/**
 * Service (handle) the (new) request.
 */
Logger::debug('Servicing request');

$colore->service();
