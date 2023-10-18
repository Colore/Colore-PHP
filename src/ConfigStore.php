<?php

namespace Colore;

class ConfigStore {
    protected static $config;

    public static function load($config): void {
        self::$config = $config;
    }

    public static function getColoreConfig() {
        return self::$config['colore'];
    }

    public static function getContextConfig() {
        return self::$config['contexts'];
    }
}
