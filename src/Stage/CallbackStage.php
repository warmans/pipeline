<?php
namespace Pipeline\Stage;

use Pipeline\Context;
use Pipeline\Workload\Task;

/**
 * Stage which allows a pipeline to be implemented using Closures instead of class instances. Class instances
 * are the preferred method though.
 *
 * @package Pipeline\Stage
 */
class CallbackStage implements StageInterface
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
     * @param Task $task
     * @param Context $context
     * @return bool|null
     */
    public function execute(Task $task, Context $context)
    {
        return $this->callback->__invoke($task, $context);
    }
}
