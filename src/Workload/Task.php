<?php
namespace Pipeline\Workload;

class Task implements \JsonSerializable
{
    private $task = array(
        'label' => '',
        'done' => false,
        'failed' => false,
        'errors' => array(),
        'metadata' => array()
    );

    /**
     * @param string $label
     * @param array $meta
     */
    public function __construct($label, array $meta = array())
    {
        $this->task['label'] = $label;
        $this->task['metadata'] = $meta;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->task['label'];
    }

    public function setDone()
    {
        $this->task['done'] = true;
    }

    /**
     * @return boolean
     */
    public function isDone()
    {
        return $this->task['done'];
    }

    /**
     * @param string $msg add a reason for the failure
     */
    public function setFailed($msg = null)
    {
        $this->task['failed'] = true;
        if ($msg) {
            $this->task['errors'][] = $msg;
        }
    }

    /**
     * @return boolean
     */
    public function isFailed()
    {
        return $this->task['failed'];
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->task['errors'];
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setMeta($name, $value)
    {
        $this->task['metadata'][$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getMeta($name)
    {
        return isset($this->task['metadata'][$name]) ? $this->task['metadata'][$name] : null;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->task;
    }
}
