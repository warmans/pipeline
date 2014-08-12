<?php
namespace Pipeline\Stage;

use Pipeline\Context;
use Pipeline\Workload\Task;

/**
 * All stages must implement this interface.
 *
 * @package Pipeline\Stage
 */
interface StageInterface
{
    /**
     * Each stage has a name which is used primarily in logging to make it simpler to track the progress of a Task.
     *
     * @return string
     */
    public function getName();

    /**
     * Execute does the actual work the given task. The same context instance will be passed to all following
     * stages.
     *
     * Returning FALSE will prevent further stages from being executed.
     *
     * @param Task $task
     * @param Context $context
     * @return bool|null
     */
    public function execute(Task $task, Context $context);
}
