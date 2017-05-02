# smxMonolog

This lib uses [Monolog](https://github.com/Seldaek/monolog/) and [Monolog Cascade](https://github.com/theorchard/monolog-cascade) to log entries to
* console
* log files
* MySQL table
* Graylog 2 via GELF

For MySQL, it uses [Monolog-MySQL](https://github.com/waza-ari/monolog-mysql), for GELF support is uses the official [GelfHandler](https://github.com/Seldaek/monolog/blob/master/src/Monolog/Handler/GelfHandler.php).

## Configuration

For global configuration, Monolog Cascade is used, here is an example YAML configuration:

```yaml
formatters:
    dashed:
        class: Monolog\Formatter\LineFormatter
        format: "%datetime%-%channel%.%level_name% - %message%\n"

handlers:
    console:
        class: Monolog\Handler\StreamHandler
        level: DEBUG
        formatter: dashed
        processors: [memory_processor]
        stream: php://stdout
    info_file_handler:
        class: Monolog\Handler\StreamHandler
        level: INFO
        formatter: dashed
        stream: ./logs/smxmonolog_info.log
    error_file_handler:
        class: Monolog\Handler\StreamHandler
        level: ERROR
        formatter: dashed
        stream: ./logs/smxmonolog_error.log
    gelf:
        class: Monolog\Handler\GelfHandler
        level: DEBUG
        publisher:
          class: Gelf\Publisher
          transport:
            class: Gelf\Transport\UdpTransport
            host: "127.0.0.1"
            port: "12200"
    mysql:
        class: MySQLHandler\MySQLHandler
        level: DEBUG
        pdo:
          class: PDO
          dsn: "mysql:dbname=smxmonolog;host=127.0.0.1"
          username: "myuser"
          passwd: "mypassword"
          options: []
        table: "logs"
        additional_fields: [line, file, origin]
        level: "Logger::DEBUG"
        bubble: "true"

processors:
    # Adds the current request URI, request method and client IP
    web_processor:
        class: Monolog\Processor\WebProcessor
    # Adds the line/file/class/method
    introspection_processor:
        class: Monolog\Processor\IntrospectionProcessor
    # Adds the current memory usage
    memory_processor:
        class: Monolog\Processor\MemoryUsageProcessor
    # Processes a log record's message according to PSR-3 rules, replacing {foo} with the value from $context['foo']
    psr_processor:
        class: Monolog\Processor\PsrLogMessageProcessor
    # Adds an array of predefined tags to a log record
    tag_processor:
        class: Monolog\Processor\TagProcessor
        tags: []

# you can use any of the loggers via SmxMonologConfig::setLoggerName($loggerName)
loggers:
    multiLogger:
        handlers: [console, info_file_handler, error_file_handler, mysql, gelf]
        processors: [psr_processor, web_processor, introspection_processor, memory_processor]
    consoleLogger:
        handlers: [console]
        processors: [psr_processor]
    fileLogger:
        handlers: [console, info_file_handler, error_file_handler]
        processors: [psr_processor, web_processor, introspection_processor]
    dbLogger:
        handlers: [console, mysql]
        processors: [psr_processor, web_processor, introspection_processor]
    gelfLogger:
        handlers: [console, gelf]
        processors: [psr_processor, web_processor, introspection_processor]

```

Just change the Graylog / GELF and/or MySQL parameters to real values!

Of course you can define _any custom loggers_ you can think of, using [any handlers and processors available](https://github.com/Seldaek/monolog/blob/master/doc/02-handlers-formatters-processors.md)!
You can also use a __PHP file / array for configuration__, see the [documentation](https://github.com/theorchard/monolog-cascade#configuration-structure).

## Usage

Here is an example how to use the SmxMonolog logger class:

```php
require_once dirname(__FILE__).'/Log/SmxMonologConfig.php';
require_once dirname(__FILE__).'/Log/SmxMonolog.php';

use Shoptimax\SmxMonolog\Log\SmxMonologConfig as SmxMonologConfig;
use Shoptimax\SmxMonolog\Log\SmxMonolog as SmxMonolog;

class myClass 
{
    public function __construct()
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
        $logger = new SmxMonolog($config, SmxMonolog::getLogLevel('INFO'));
        // log with additional fields
        $logger->log("Hello, World!", array('foo' => 'bar'));
        // change log level
        $logger->setLogLevel(SmxMonolog::getLogLevel('NOTICE'));
        $logger->log("Hello, Notice!");
        // set a custom log level for a message
        $logger->log("Hello, Custom Alert!", array(), SmxMonolog::getLogLevel('ALERT'));
        // use dedicated log functions with fix level
        $logger->debug("Will have {food} for {meal}", array('food' => 'fish', 'meal' => 'breakfast'));
        $logger->warning("Testing this warning");
        $logger->error("Doh, an error!");
    }
}

```
