<?php
namespace Pipeline\Stage;

use Pipeline\Context;
use Pipeline\Workload\Task;

/**
 * Stage which allows a pipeline to be implemented using Closures instead of class instances.
 *
 * @package Pipeline\Stage
 */
class CallbackStage implements StageInterface
{
    private $name;
    private $callback;

    public function __construct($name, \Closure $callback)
    {
        $this->name = $name;
        $this->callback = $callback;
    }

    public function getName()
    {
        return $this->name;
    }

    public function execute(Task $task, Context $context)
    {
        return $this->callback->__invoke($task, $context);
    }
}
