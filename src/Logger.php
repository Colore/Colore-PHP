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

    public static function setBasePath(string $basePath): void {
        self::$basePath = $basePath;
    }

    protected static function log(array $messageArgs): void {
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

    public static function setLogLevel($logLevel = LOG_NOTICE): void {
        self::$logLevel = $logLevel;
    }

    public static function shouldProcess(int $logLevel): bool {
        return $logLevel <= self::$logLevel;
    }

    public static function shouldDebug() {
        return self::shouldProcess(LOG_DEBUG);
    }

    /**
     * @return never
     */
    public static function fatal(string ...$errorMessage) {
        self::log($errorMessage);

        die('A fatal error occured.');
    }

    //
    // Below are all generated
    //


    public static function critical(string ...$messageArgs): void {
        if (self::shouldProcess(LOG_CRIT)) {
            self::log($messageArgs);
        }
    }

    public static function error(string ...$messageArgs): void {
        if (self::shouldProcess(LOG_ERR)) {
            self::log($messageArgs);
        }
    }

    public static function debug(string ...$messageArgs): void {
        if (self::shouldProcess(LOG_DEBUG)) {
            self::log($messageArgs);
        }
    }

    public static function trace(string ...$messageArgs): void {
        if (self::shouldProcess(LOG_TRACE)) {
            self::log($messageArgs);
        }
    }
}
