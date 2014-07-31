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

    public function __construct($label, array $meta = array())
    {
        $this->task['label'] = $label;
        $this->task['metadata'] = $meta;
    }

    public function getLabel()
    {
        return $this->task['label'];
    }

    public function setDone()
    {
        $this->task['done'] = true;
    }

    public function isDone()
    {
        return $this->task['done'];
    }

    public function setFailed($msg = null)
    {
        $this->task['failed'] = true;
        if ($msg) {
            $this->task['errors'][] = $msg;
        }
    }

    public function isFailed()
    {
        return $this->task['failed'];
    }

    public function getErrors()
    {
        return $this->task['errors'];
    }

    public function setMeta($name, $value)
    {
        $this->task['metadata'][$name] = $value;
    }

    public function getMeta($name)
    {
        return isset($this->task['metadata'][$name]) ? $this->task['metadata'][$name] : null;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return $this->task;
    }
}
