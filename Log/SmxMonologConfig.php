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

/**
 * Class SmxMonologConfig
 *
 * @package Shoptimax\SmxMonolog\Log
 */
class SmxMonologConfig
{
    /**
     * The logger name as defined in the "loggers" array
     * in the monolog-cascade configuration
     *
     * @var string
     */
    protected $loggerName = "fileLogger";

    /**
     * The cascade monolog configuration
     *
     * @var string|array
     */
    protected $fileConfig = null;

    /**
     * Additional context field
     *
     * @var string
     */
    protected $origin = '';

    /**
     * Returns a file path, e.g. to a yaml config file
     * or a PHP array with config settings,
     * see https://github.com/theorchard/monolog-cascade
     *
     * @return string|array
     */
    public function getFileConfig()
    {
        return $this->fileConfig;
    }

    /**
     * Set the file configuration for monolog cascade
     *
     * @param string|array $fileConfig The config
     *
     * @return mixed
     */
    public function setFileConfig($fileConfig)
    {
        $this->fileConfig = $fileConfig;
    }

    /**
     * Get the logger name as defined in the "loggers" array
     * in the monolog-cascade configuration
     *
     * @return string
     */
    public function getLoggerName()
    {
        return $this->loggerName;
    }

    /**
     * Set the logger name as defined in the "loggers" array
     * in the monolog-cascade configuration
     *
     * @param String $loggerName The logger name, e.g. fileLogger or
     *                           dbLogger
     *
     * @return null
     */
    public function setLoggerName($loggerName)
    {
        $this->loggerName = $loggerName;
    }

    /**
     * Get the "ctxt_origin" for additional info
     *
     * @return string
     */
    public function getCtxtOrigin()
    {
        return $this->origin;
    }
    /**
     * Set the "ctxt_origin" for additional info
     *
     * @param string $facility The facility name
     *
     * @return null
     */
    public function setCtxtOrigin($facility)
    {
        $this->origin = $facility;
    }
}