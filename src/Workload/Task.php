<?php
namespace Pipeline\Workload;

/**
 * Tasks don't do anything they just describe some task that must be performed in stages.
 *
 * @package Pipeline\Workload
 */
class Task implements \JsonSerializable
{
    const STATUS_DONE = 1;
    const STATUS_FAILED = -1;
    const STATUS_UNKNOWN = 0;

    private $task = array(
        'label' => '',
        'status' => self::STATUS_UNKNOWN,
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
        $this->task['status'] = self::STATUS_DONE;
    }

    /**
     * @return boolean
     */
    public function isDone()
    {
        return $this->task['status'] === self::STATUS_DONE;
    }

    /**
     * @param string $msg add a reason for the failure
     */
    public function setFailed($msg = null)
    {
        $this->task['status'] = self::STATUS_FAILED;
        if ($msg) {
            $this->task['errors'][] = $msg;
        }
    }

    /**
     * @return boolean
     */
    public function isFailed()
    {
        return $this->task['status'] === self::STATUS_FAILED;
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
