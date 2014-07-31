<?php
namespace Pipeline;

class WorkloadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Workload
     */
    private $workload;

    public function setUp()
    {
        $this->workload = new Workload();
    }

    public function testAddAndGetFirstTask()
    {
        $this->workload->addTask(new Workload\Task('foo'));
        $this->workload->addTask(new Workload\Task('bar'));
        $this->assertEquals('foo', $this->workload->getFirst()->getLabel());
    }

    public function testGetLastTask()
    {
        $this->workload->addTask(new Workload\Task('foo'));
        $this->workload->addTask(new Workload\Task('bar'));
        $this->assertEquals('bar', $this->workload->getLast()->getLabel());
    }

    public function testGetTasksReturnsTraversable()
    {
        $this->workload->addTask(new Workload\Task('foo'));
        $this->workload->addTask(new Workload\Task('bar'));

        $this->assertCount(2, $this->workload->getTasks());
    }

    public function testGetTasksContainsTasks()
    {
        $this->workload->addTask(new Workload\Task('foo'));
        $this->workload->addTask(new Workload\Task('bar'));

        $tasks = $this->workload->getTasks();

        $this->assertEquals('foo', $tasks[0]->getLabel());
        $this->assertEquals('bar', $tasks[1]->getLabel());
    }

    public function testRemoveTaskByLabel()
    {
        $this->workload->addTask(new Workload\Task('foo'));
        $this->workload->addTask(new Workload\Task('bar'));
        $this->workload->removeTaskByLabel('bar');

        $this->assertCount(1, $this->workload->getTasks());
    }

    public function testRemoveTaskByLabelReindexesTasks()
    {
        $this->workload->addTask(new Workload\Task('foo'));
        $this->workload->addTask(new Workload\Task('bar'));
        $this->workload->removeTaskByLabel('foo');

        $tasks = $this->workload->getTasks();
        $this->assertEquals('bar', $tasks[0]->getLabel(), 'bar did not move to position 0 after foo was removed');
    }
}
