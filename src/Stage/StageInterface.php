<?php
namespace Pipeline\Stage;

use Pipeline\Context;
use Pipeline\Workload\Task;

interface StageInterface
{
    public function getName();

    public function execute(Task $task, Context $context);
} 