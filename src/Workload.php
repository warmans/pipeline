<?php
namespace Pipeline;

class Workload
{
    private $tasks = array();

    public function addTask(Workload\Task $task)
    {
        $this->tasks[]  = $task;
    }

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

    public function getTasks()
    {
        return $this->tasks;
    }

    public function getFirst()
    {
        return $this->tasks[0];
    }

    public function getLast()
    {
        return $this->tasks[count($this->tasks)-1];
    }
}
