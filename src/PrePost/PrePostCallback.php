<?php
namespace Pipeline\PrePost;

use Pipeline\Context;
use Pipeline\Workload;

/**
 * @package Pipeline\PrePost
 */
class PrePostCallback implements PrePostInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \Closure
     */
    private $callback;

    /**
     * @param $name
     * @param \Closure $callback
     */
    public function __construct($name, \Closure $callback)
    {
        $this->name = $name;
        $this->callback = $callback;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Workload $workload
     * @param Context $context
     * @return mixed
     */
    public function execute(Workload $workload, Context $context)
    {
        return $this->callback->__invoke($workload, $context);
    }
}
