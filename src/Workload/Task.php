<?php
namespace Pipeline\Workload;

class Task
{
    private $label;
    private $done = false;
    private $failed = false;
    private $errors = array();
    private $metadata = array();

    public function __construct($label, array $meta = array())
    {
        $this->label = $label;
        $this->metadata = $meta;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setDone()
    {
        $this->done = true;
    }

    public function isDone()
    {
        return $this->done;
    }

    public function setFailed($msg = null)
    {
        $this->failed = true;
        if ($msg) {
            $this->errors[] = $msg;
        }
    }

    public function isFailed()
    {
        return $this->failed;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function setMeta($name, $value)
    {
        $this->metadata[$name] = $value;
    }

    public function getMeta($name)
    {
        return isset($this->metadata[$name]) ? $this->metadata[$name] : null;
    }
}
