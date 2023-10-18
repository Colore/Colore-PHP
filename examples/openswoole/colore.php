<?php

define('BASEDIR', __DIR__);

require_once __DIR__ . '/../../vendor/autoload.php';

use Colore\Logger;
use Colore\Engine;

use OpenSwoole\Http\Server;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;

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

$server = new Server('0.0.0.0', 9501);

$server->on('Start', function (Server $server) {
    echo "OpenSwoole http server is started at http://0.0.0.0:9501\n";
});

$server->on('Request', function (Request $request, Response $response) use ($colore) {
    call_user_func_array([$colore, 'service'], [&$request, &$response]);
});

$server->start();
