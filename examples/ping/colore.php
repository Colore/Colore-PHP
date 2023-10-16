<?php

define('BASEDIR', __DIR__);

require_once __DIR__ . '/../../vendor/autoload.php';

use Colore\ConfigStore;
use Colore\Logger;
use Colore\Engine;
use Colore\Helpers\PHPContextHelper;

Logger::setLogLevel(LOG_TRACE);
Logger::setBasePath(__DIR__);

ConfigStore::load(json_decode(file_get_contents(__DIR__ . '/config.json'), true));

$phpContextHelper = new PHPContextHelper();

/**
 * Create a new ColoreEngine instance.
 */
Logger::debug('Instantiating ColoreEngine');

$colore = new Engine(ConfigStore::getColoreConfig());

/**
 * Service (handle) the (new) request.
 */
Logger::debug('Servicing request');

$colore->service();
