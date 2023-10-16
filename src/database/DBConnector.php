<?php

namespace Colore\Database;

use Colore\Logger;

class DBConnector
{
    private static $_instance = null;
    protected $_pdo;

    protected function __construct()
    {
        global $config;

        // Create a new database connection
        $this->_pdo = new \PDO(
            $config['db']['dsn'],
            $config['db']['user'],
            $config['db']['pass'],
            [\PDO::ATTR_PERSISTENT => true]
        );

        // Set default mode to associative
        $this->_pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    public static function getInstance()
    {
        global $config;

        if (self::$_instance == null) {
            try {
                self::$_instance = new DBconnector();
            } catch (\PDOException $e) {
                Logger::critical('Connection failed: %s - %s ', $e->getMessage(), $config['db']['dsn']);
                die('Fatal DB connection error');
            }
        }

        return self::$_instance;
    }

    public function __clone()
    {
        return false;
    }

    public function __wakeup()
    {
        return false;
    }

    public function prepare($statement, $opts = [])
    {
        Logger::debug($statement);

        return $this->_pdo->prepare($statement, $opts);
    }

    public function exec($statement)
    {
        return $this->_pdo->exec($statement);
    }

    public function query($statement)
    {
        return $this->_pdo->query($statement);
    }

    public function lastInsertId($name = null)
    {
        return $this->_pdo->lastInsertId($name);
    }

    public function mappedQuery(array $sqlMapped)
    {
        Logger::debug('Running for: [%s]', print_r($sqlMapped, 1));

        // generated SQL
        $generatedSQLInfo = SQLmapper::generateSQL($sqlMapped);

        Logger::debug('Running for: [%s]', print_r($generatedSQLInfo, 1));

        // SQL logic
        $queryHandler = $this->prepare($generatedSQLInfo['statement']);
        $queryResult = $queryHandler->execute($generatedSQLInfo['arguments']);

        if ($queryResult) {
            Logger::debug('Successful query for: [%s]/[%d]', $generatedSQLInfo['statement'], $queryHandler->rowCount());

            if ($sqlMapped['action'] == 'select' && $queryHandler->rowCount() == 1) {
                return $queryHandler->fetch();
            } elseif ($sqlMapped['action'] == 'select' && $queryHandler->rowCount() > 1) {
                return $queryHandler->fetchAll();
            } else {
                return true;
            }
        } else {
            Logger::debug(
                'Error for query[%s]: [%s]',
                print_r($queryHandler->errorInfo(), 1),
                print_r($generatedSQLInfo, 1)
            );

            return false;
        }
    }
}
