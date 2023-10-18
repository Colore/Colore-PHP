<?php

namespace Colore\Database;

use Colore\Logger;


class DBConnector {
    private static $instance = null;
    protected $pdo;

    protected function __construct() {
        global $config, $pdoOpts;

        // Create a new database connection
        $this->pdo = new \PDO($config['db']['dsn'], $config['db']['user'], $config['db']['pass'], $pdoOpts);

        // Set default mode to associative
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    public function __clone() {
        return false;
    }

    public function __wakeup() {
        return false;
    }

    public function prepare($statement, $opts = []) {
        Logger::debug($statement);

        return $this->pdo->prepare($statement, $opts);
    }

    public function exec($statement) {
        return $this->pdo->exec($statement);
    }

    public function query($statement) {
        return $this->pdo->query($statement);
    }

    public function lastInsertId($name = null) {
        return $this->pdo->lastInsertId($name);
    }
}
