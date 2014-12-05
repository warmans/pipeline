<?php
namespace Pipeline;

/**
 * A context instance is passed between tasks allowing communication with later tasks. For example one task could
 * download a file and store it's location in the context then a later task could process the file.
 * The context can also be assigned a logger closure which allows you to delegate messages back to some applicable
 * handler e.g. Symfony Console Output in the case of a CLI application.
 *
 *
 * @package Pipeline
 */
class Context
{
    /**
     * @var \Closure
     */
    private $logger;

    /**
     * @var array
     */
    private $data = array();

    /**
     * @var array
     */
    private $logPrefixes = array();

    public function __construct()
    {
        $this->logger = function ($msg, $writeln = true) {
            return;
        };
    }

    /**
     * Supply a logger from the calling scope e.g. STDOUT logger form console application.
     *
     * @param \Closure $logger
     */
    public function setLogger(\Closure $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Push a prefix onto the top of the prefix stack
     *
     * @param string $prefix
     */
    public function pushPrefix($prefix)
    {
        $this->logPrefixes[] = $prefix;
    }

    /**
     * Pop a prefix off the stack
     */
    public function popPrefix()
    {
        array_pop($this->logPrefixes);
    }

    /**
     * @param string $msg
     * @param bool $writeln
     */
    public function log($msg, $writeln=true)
    {
        $prefixed = '';
        foreach ($this->logPrefixes as $prefix) {
            $prefixed .= $prefixed ? ' '.$prefix : $prefix;
        }
        $msg = $prefixed ? $prefixed.' '.$msg : $msg;
        $this->logger->__invoke($msg, $writeln);
    }

    /**
     * @param string $name
     * @param mixed $data
     */
    public function setData($name, $data)
    {
        $this->data[$name] = $data;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getData($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }
}
