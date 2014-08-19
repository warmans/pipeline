<?php
namespace Pipeline;

use Pipeline\PrePost\PrePostInterface;
use Pipeline\Stage\StageInterface;
use Pipeline\Workload\Task;

/**
 * A pipeline takes a workload and passes each task through a series of stages. Each task has a context
 * instance which is shared between stages (but not tasks) allowing communication with later stages. The initial
 * context instance given to execute is cloned for each task so you can share information between tasks using this
 * shared context.
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
     * @var array
     */
    private $setup = array();

    /**
     * @var array
     */
    private $teardown = array();

    /**
     * All setup logic is invoked ONCE before a pipeline is executed.
     *
     * @param PrePost\PrePostInterface $setup
     */
    public function addSetup(PrePostInterface $setup)
    {
        $this->setup[] = $setup;
    }

    /**
     * All teardown logic is invoked ONCE after a pipeline has executed.
     */
    public function addTeardown(PrePostInterface $teardown)
    {
        $this->teardown[] = $teardown;
    }

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
     * @return bool
     */
    public function execute(Workload $workload, Context $context)
    {
        foreach($this->setup as $setup) {
            $setup->execute($workload, $context);
        }

        $failed = 0;
        foreach($workload->getTasks() as $task) {
            //each task executes in a context
            $taskContext = clone $context;
            $taskContext->log('');
            $taskContext->log('TASK :: '.$task->getLabel().'');

            $taskContext->pushPrefix('|');
            $result = $this->executeStages($task, $taskContext);
            $taskContext->popPrefix();

            if ($result === false) {
                $failed++;
            }

            $taskContext->log((false === $result) ? 'FAILED' : 'COMPLETED');
        }

        foreach($this->teardown as $teardown) {
            $teardown->execute($workload, $context);
        }

        return ($failed > 0) ? false : true;
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
                $context->log('pipeline ended as task is done');
                return true;
            }

            //task has failed
            if ($task->isFailed()) {
                $context->log('pipeline aborted as task failed');
                return false;
            }
        }
        return true;
    }
}
