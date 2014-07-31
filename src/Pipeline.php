<?php
namespace Pipeline;

use Pipeline\Stage\StageInterface;
use Pipeline\Workload\Task;

/**
 * A pipeline takes a workload and passes each workload task through a series of stages. Each task has a context
 * instance which is shared between stages (but not tasks) allowing communication with later stages. The initial
 * context given at execute is cloned for each task so you can share
 *
 * @package Pipeline
 */
class Pipeline
{
    /**
     * @var array
     */
    private $stages = array();

    /**
     * @param StageInterface $stage
     * @throws \RuntimeException
     */
    public function addStage(StageInterface $stage)
    {
        if (array_key_exists($stage->getName(), $this->stages)) {
            throw new \RuntimeException('Duplicate stage name');
        }
        $this->stages[$stage->getName()] = $stage;
    }

    /**
     * @return array
     */
    public function getStages()
    {
        return $this->stages;
    }

    /**
     * @param Workload $workload
     * @param Context $context
     */
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

    /**
     * @param Task $task
     * @param Context $context
     * @return bool
     */
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
