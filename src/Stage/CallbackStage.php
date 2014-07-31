<?php
namespace Pipeline\Stage;

use Pipeline\Context;
use Pipeline\Workload\Task;

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
