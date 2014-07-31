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

    public function setLogger(\Closure $logger)
    {
        $this->logger = $logger;
    }

    public function pushPrefix($prefix)
    {
        $this->logPrefixes[] = $prefix;
    }

    public function popPrefix()
    {
        array_pop($this->logPrefixes);
    }

    public function log($msg, $writeln=true)
    {
        $prefixed = '';
        foreach ($this->logPrefixes as $prefix) {
            $prefixed .= $prefixed ? ' '.$prefix : $prefix;
        }
        $msg = $prefixed ? $prefixed.' '.$msg : $msg;
        $this->logger->__invoke($msg, $writeln);
    }

    public function setData($name, $data)
    {
        $this->data[$name] = $data;
    }

    public function getData($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }
}
