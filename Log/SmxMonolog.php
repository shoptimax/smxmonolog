<?php
/**
 * (c) shoptimax GmbH, Nürnberg
 *
 * @package   SmxMonolog
 * @author    shoptimax GmbH <info@shoptimax.de>
 * @copyright 2017 shoptimax GmbH
 * @link      http://www.shoptimax.de
 */

namespace Shoptimax\SmxMonolog\Log;

if (file_exists(dirname(__FILE__) . '/../vendor/autoload.php')) {
    include_once dirname(__FILE__) . '/../vendor/autoload.php';
} elseif (file_exists(dirname(__FILE__) . '/../../../../vendor/autoload.php')) {
    include_once dirname(__FILE__) . '/../../../../vendor/autoload.php';
}

use Cascade\Cascade;
use Monolog\Logger as Logger;

class SmxMonolog
{
    /**
     * Logger Configuration
     *
     * @var SmxMonologConfig
     */
    protected $smxMonologConfig = null;

    /**
     * The available log levels,
     * coming straight from Monolog/Logger
     *
     * @var array
     */
    public static $aLogLevels = array();

    /**
     * Our current log level
     *
     * @var int
     */
    protected $logLevel = 0;

    /**
     * SmxMonolog constructor.
     *
     * @param SmxMonologConfig $smxMonologConfig The class holding the
     *                                           Cascade file configuration
     * @param int              $iLogLevel        The Monolog log level, see
     *                                           https://github.com/Seldaek/monolog/blob/master/src/Monolog/Logger.php
     */
    public function __construct(SmxMonologConfig $smxMonologConfig, $iLogLevel = 0)
    {
        $this->smxMonologConfig = $smxMonologConfig;
        $fileConfig = $smxMonologConfig->getFileConfig();
        if ($fileConfig == null) {
            error_log("SmxMonologConfig fileConfig is null, disabling smxMonolog!");
            return;
        }
        // use Monolog log levels
        if (!count(self::$aLogLevels)) {
            self::$aLogLevels = Logger::getLevels();
        }
        if ($iLogLevel < 1) {
            // set default log level
            $this->logLevel = self::$aLogLevels['INFO'];
        } else {
            $this->logLevel = $iLogLevel;
        }
        Cascade::fileConfig($fileConfig);
    }

    /**
     * Get loglevel by name
     *
     * @param string $logLevelName The level name, e.g. DEBUG
     *
     * @return mixed
     */
    public static function getLogLevel($logLevelName)
    {
        if (!count(self::$aLogLevels)) {
            self::$aLogLevels = Logger::getLevels();
        }
        $logLevelName = strtoupper($logLevelName);
        if (isset(self::$aLogLevels[$logLevelName])) {
            return self::$aLogLevels[$logLevelName];
        }
    }

    /**
     * Set the log level
     *
     * @param int $iLogLevel The log level
     *
     * @return null
     */
    public function setLogLevel($iLogLevel)
    {
        $this->logLevel = $iLogLevel;
    }

    /**
     * The main logging function
     *
     * @param string $message   The log message
     * @param array  $context   Additional context info
     * @param int    $iLogLevel Custom log level for message
     *
     * @return null
     */
    public function log($message, array $context = array(), $iLogLevel = 0)
    {
        if ($iLogLevel > 0) {
            $this->logLevel = $iLogLevel;
        }
        // add extra "origin" ctxt field for filtering in graylog, mysql etc.
        // since "facility" is set to the logger configuration name
        if (!isset($context['origin']) && ($facility = $this->smxMonologConfig->getCtxtOrigin()) != '') {
            $context['origin'] = $facility;
        }
        $logger = Cascade::getLogger($this->smxMonologConfig->getLoggerName());
        switch ($this->logLevel) {
        case Logger::DEBUG:
            $logger->debug($message, $context);
            break;
        case Logger::INFO:
            $logger->info($message, $context);
            break;
        case Logger::NOTICE:
            $logger->notice($message, $context);
            break;
        case Logger::WARNING:
            $logger->warning($message, $context);
            break;
        case Logger::ERROR:
            $logger->error($message, $context);
            break;
        case Logger::CRITICAL:
            $logger->critical($message, $context);
            break;
        case Logger::ALERT:
            $logger->alert($message, $context);
            break;
        case Logger::EMERGENCY:
            $logger->emergency($message, $context);
            break;
        default:
            $logger->info($message, $context);
        }
    }

    /**
     * Delegate methods like debug(), info() etc.
     * straight to Monolog
     *
     * @param string $name      The called function name
     * @param array  $arguments The passed parameters
     *
     * @return null
     */
    public function __call($name, $arguments)
    {
        $supportedFunctions = array('debug', 'info', 'notice', 'warning', 'warn', 'error', 'critical', 'alert', 'emergency');
        if (is_array($arguments) && in_array($name, $supportedFunctions)) {
            $msg = $arguments[0];
            $context = (isset($arguments[1]) && $arguments[1] !== null) ? $arguments[1] : array();
            if (!isset($context['origin']) && ($facility = $this->smxMonologConfig->getCtxtOrigin()) != '') {
                $context['origin'] = $facility;
            }
            $logger = Cascade::getLogger($this->smxMonologConfig->getLoggerName());
            $logger->$name($msg, $context);
        }
    }

}