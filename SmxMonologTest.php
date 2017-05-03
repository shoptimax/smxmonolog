<?php
/**
 * (c) shoptimax GmbH, Nürnberg
 *
 * @package   SmxMonolog
 * @author    shoptimax GmbH <info@shoptimax.de>
 * @copyright 2017 shoptimax GmbH
 * @link      http://www.shoptimax.de
 */
require_once dirname(__FILE__).'/Log/SmxMonologConfig.php';
require_once dirname(__FILE__).'/Log/SmxMonolog.php';

use Shoptimax\SmxMonolog\Log\SmxMonologConfig as SmxMonologConfig;
use Shoptimax\SmxMonolog\Log\SmxMonolog as SmxMonolog;

/**
 * Class SmxMonologTest, gives some usage examples for SmxMonolog
 */
class SmxMonologTest
{
    /**
     * SmxMonologTest constructor.
     *
     * @return null
     */
    public static function constructStatic()
    {
        // we use a YAML config here
        $yamlConfig = dirname(__FILE__).'/logconfig.yaml';
        $config = new SmxMonologConfig();
        $config->setFileConfig($yamlConfig);
        // choose a logger configuration from the yaml loggers
        $config->setLoggerName('multiLogger');
        // set generic origin for filtering etc.
        $config->setCtxtOrigin('www.mydomain.de');

        // new logger, set config and initial log level
        $logger = new SmxMonolog($config, 'INFO');
        // log with additional fields
        $logger->log("Hello, World!", array('foo' => 'bar'));
        // change log level
        $logger->setLogLevel('NOTICE');
        $logger->log("Hello, Notice!");
        // set a custom log level for a message
        $logger->log("Hello, Custom Alert!", array(), 'ALERT');
        // use dedicated log functions with fix level
        $logger->debug("Will have {food} for {meal}", array('food' => 'fish', 'meal' => 'breakfast'));
        $logger->warning("Testing this warning");
        $logger->error("Doh, an error!");
    }
}
SmxMonologTest::constructStatic();