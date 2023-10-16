<?php

namespace Colore;

if (!defined('LOG_VERBOSE')) {
    define('LOG_VERBOSE', LOG_DEBUG + 1);
}

if (!defined('LOG_TRACE')) {
    define('LOG_TRACE', LOG_VERBOSE + 1);
}

class Logger {
    private static $basePath = '';
    private static $logLevel = LOG_NOTICE;

    public static function getBasePath() {
        return self::$basePath;
    }

    public static function setBasePath($basePath) {
        self::$basePath = $basePath;
    }

    protected static function log($messageArgs) {
        $traceInfo = debug_backtrace()[2];
        $logInfo = debug_backtrace()[1];

        $method = '';

        if (isset($traceInfo['class']) && isset($traceInfo['type'])) {
            $method .= $traceInfo['class'];
            $method .= $traceInfo['type'];
            $method .= $traceInfo['function'];
        } else {
            if (self::$basePath != '') {
                $method = str_replace(self::$basePath, '', $traceInfo['file']);
            } else {
                $method = $traceInfo['file'];
            }
        }

        // $messageArgs = func_get_args();

        if (is_array($messageArgs) && count($messageArgs) > 1) {
            $logFmt = array_shift($messageArgs);

            $logString = vsprintf($logFmt, $messageArgs);
        } else {
            $logString = $messageArgs[0];
        }

        error_log(sprintf('%s - %s: %s', strtoupper($logInfo['function']), $method, $logString));
    }

    public static function getLogLevel() {
        return self::$logLevel;
    }

    public static function setLogLevel($logLevel = LOG_NOTICE) {
        self::$logLevel = $logLevel;
    }

    public static function shouldProcess($logLevel) {
        return $logLevel <= self::$logLevel;
    }

    public static function shouldDebug() {
        return self::shouldProcess(LOG_DEBUG);
    }

    public static function fatal(...$errorMessage) {
        self::log($errorMessage);

        die('A fatal error occured.');
    }

    //
    // Below are all generated
    //
    public static function emerg(...$messageArgs) {
        if (self::shouldProcess(LOG_EMERG)) {
            self::log($messageArgs);
        }
    }

    public static function alert(...$messageArgs) {
        if (self::shouldProcess(LOG_ALERT)) {
            self::log($messageArgs);
        }
    }

    public static function critical(...$messageArgs) {
        if (self::shouldProcess(LOG_CRIT)) {
            self::log($messageArgs);
        }
    }

    public static function error(...$messageArgs) {
        if (self::shouldProcess(LOG_ERR)) {
            self::log($messageArgs);
        }
    }

    public static function warning(...$messageArgs) {
        if (self::shouldProcess(LOG_WARNING)) {
            self::log($messageArgs);
        }
    }

    public static function notice(...$messageArgs) {
        if (self::shouldProcess(LOG_NOTICE)) {
            self::log($messageArgs);
        }
    }

    public static function info(...$messageArgs) {
        if (self::shouldProcess(LOG_INFO)) {
            self::log($messageArgs);
        }
    }

    public static function debug(...$messageArgs) {
        if (self::shouldProcess(LOG_DEBUG)) {
            self::log($messageArgs);
        }
    }

    public static function verbose(...$messageArgs) {
        if (self::shouldProcess(LOG_VERBOSE)) {
            self::log($messageArgs);
        }
    }

    public static function trace(...$messageArgs) {
        if (self::shouldProcess(LOG_TRACE)) {
            self::log($messageArgs);
        }
    }
}
