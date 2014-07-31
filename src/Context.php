<?php
namespace Pipeline;

class Context
{
    private $logger;
    private $data = array();
    private $logPrefixes = array();

    public function __construct()
    {
        $this->logger = function ($msg, $writeln = true) {
            return;
        };
    }

    /**
     * Supply an applicable logger from the outer context
     *
     * @param callable $logger
     */
    public function setLogger(\Closure $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Push a prefix onto the top of the prefix stack
     *
     * @param $prefix
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
