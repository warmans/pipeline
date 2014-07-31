<?php
namespace Pipeline;

use Pipeline\Stage\StageInterface;
use Pipeline\Workload\Task;

class Pipeline
{
    private $stages = array();

    public function addStage(StageInterface $stage)
    {
        if (array_key_exists($stage->getName(), $this->stages)) {
            throw new \RuntimeException('Duplicate stage name');
        }
        $this->stages[$stage->getName()] = $stage;
    }

    public function getStages()
    {
        return $this->stages;
    }

    public function execute(Workload $workload, Context $context)
    {
        foreach($workload->getTasks() as $task) {
            //each task executes in a context
            $taskContext = clone $context;
            $taskContext->log('');
            $taskContext->log('TASK :: '.$task->getLabel().'');

            $taskContext->pushPrefix('|');
            $result = $this->executeStages($task, $taskContext);
            $taskContext->popPrefix();

            $taskContext->log((false === $result) ? 'FAILED' : 'COMPLETED');
        }
    }

    protected function executeStages(Task $task, Context $context)
    {
        foreach ($this->getStages() as $name => $stage) {
            $context->log("STAGE :: $name");

            try {
                //trigger abort by returning false
                $context->pushPrefix('|');
                $result = $stage->execute($task, $context);
                $context->popPrefix();

                if (false === $result) {
                    $context->log("pipeline manually halted");
                    return false;
                }
            } catch (\Exception $e) {
                $context->log("exception caught: ".$e->getMessage());
                return false;
            }

            //check if task has completed prematurely
            if ($task->isDone()) {
                $context->log('pipeline completed prematurely');
                return true;
            }

            //task has failed
            if ($task->isFailed()) {
                $context->log('pipeline aborted due to failed task');
                return false;
            }
        }
        return true;
    }
}
