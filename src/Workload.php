<?php
namespace Pipeline;

/**
 * A workload is just a container for tasks. The pipeline requires a workload and will run each task though
 * applicable stages.
 *
 * @package Pipeline
 */
class Workload
{
    /**
     * @var array
     */
    private $tasks = array();

    /**
     * @param Workload\Task $task
     */
    public function addTask(Workload\Task $task)
    {
        $this->tasks[]  = $task;
    }

    /**
     * @param $label
     */
    public function removeTaskByLabel($label)
    {
        foreach ($this->getTasks() as $key => $task) {
            if ($task->getLabel() == $label) {
                unset($this->tasks[$key]);
            }
        }

        //reindex tasks
        $this->tasks = array_values($this->tasks);
    }

    /**
     * @return array
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * @return mixed
     */
    public function getFirst()
    {
        return $this->tasks[0];
    }

    /**
     * @return mixed
     */
    public function getLast()
    {
        return $this->tasks[count($this->tasks)-1];
    }
}
