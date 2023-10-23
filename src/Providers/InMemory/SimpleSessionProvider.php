<?php

namespace Colore\Providers\InMemory;

use Colore\Logger;
use Ramsey\Uuid\Uuid;

class SimpleSessionProvider {
    protected static $gcInterval = 900;
    protected static $gcLastRun = 0;

    protected static $sessions = [];

    private $sessionId;

    protected static function runGC(): void {
        $runTime = time();

        foreach (self::$sessions as $sessionId => $sessionStore) {
            if ($sessionStore['expiry'] < $runTime) {
                unset(self::$sessions[$sessionId]);
            }
        }
    }

    private function __construct($sessionId = null) {
        if (is_null($sessionId)) {
            $sessionId = Uuid::uuid4()->toString();

            self::$sessions[$sessionId] = [
                'data' => [],
                'expiry' => self::$gcInterval + time()
            ];
        }

        $this->sessionId = $sessionId;
    }

    public static function getSession($sessionId = null): self {
        Logger::debug('sessionId: %s', $sessionId ?? 'null');

        if (self::$gcLastRun < time()) {
            self::$gcLastRun += self::$gcInterval;
            self::runGC();
        }

        return new SimpleSessionProvider($sessionId);
    }

    public function getSessionId() {
        return $this->sessionId;
    }

    public function __isset($sessionVariable) {
        return isset(self::$sessions[$this->sessionId]['data'][$sessionVariable]);
    }

    public function __unset($sessionVariable) {
        unset(self::$sessions[$this->sessionId]['data'][$sessionVariable]);
    }
}
