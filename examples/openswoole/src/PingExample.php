<?php

namespace Colore\Examples\OpenSwoole;

use Colore\Interfaces\Adapters\IRequestAdapter;

/**
 * The Ping class is an example class for remoting.
 */
class PingExample {
    public function reply(IRequestAdapter $cro): void {
        if ($cro->getRequestArgument('message')) {
            $cro->setRenderProperty('message', $cro->getRequestArgument('message'));
        } else {
            $cro->setRenderProperty('message', 'message in a bottle');
        }
    }
}
