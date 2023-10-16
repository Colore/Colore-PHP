<?php

namespace Colore\Examples\Ping;

use Colore\Interfaces\RequestHelper;

/**
 * The Ping class is an example class for remoting.
 */
class PingExample {
    public function reply(RequestHelper $cro) {
        if ($cro->getRequestArgument('message')) {
            $cro->setRenderProperty('message', $cro->getRequestArgument('message'));
        }
    }
}
