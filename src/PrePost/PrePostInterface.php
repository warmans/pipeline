<?php
namespace Pipeline\PrePost;

use Pipeline\Context;
use Pipeline\Workload\Task;
use Pipeline\Workload;

/**
 * All stages must implement this interface.
 *
 * @package Pipeline\Stage
 */
interface PrePostInterface
{
    /**
     * Each stage has a name which is used primarily in logging to make it simpler to track the progress of a Task.
     *
     * @return string
     */
    public function getName();

    /**
     * @param Workload $task
     * @param Context $context
     * @return mixed
     */
    public function execute(Workload $workload, Context $context);
}
