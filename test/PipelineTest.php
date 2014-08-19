<?php
namespace Pipeline;

use Pipeline\Stage\CallbackStage;

class PipelineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Pipeline\Pipeline
     */
    private $object;

    public function setUp()
    {
        $this->object = new \Pipeline\Pipeline();
    }

    public function testSetGetStages()
    {
        $this->object->addStage(new CallbackStage('foo', function(Workload\Task $task) {
            return true;
        }));

        $stages = $this->object->getStages();
        $this->assertEquals('foo', $stages['foo']->getName());
    }

    public function testExecuteExecutesStages()
    {
        $stagesExecuted = 0;

        //setup single task
        $workload = new Workload();
        $workload->addTask(new Workload\Task('foo'));

        //setup two stages
        $this->object->addStage(new CallbackStage('first-stage', function (Workload\Task $task) use (&$stagesExecuted) {
            $stagesExecuted++;
        }));

        $this->object->addStage(new CallbackStage('second-stage', function (Workload\Task $task) use (&$stagesExecuted) {
            $stagesExecuted++;
        }));

        //execute
        $this->object->execute($workload, new Context());

        //1 x 2 = 2
        $this->assertEquals(2, $stagesExecuted);
    }

    public function testStageCanHaltPipeline()
    {
        $stagesExecuted = array();

        //setup single task
        $workload = new Workload();
        $workload->addTask(new Workload\Task('foo'));

        //setup two stages
        $this->object->addStage(new CallbackStage('first-stage', function (Workload\Task $task) use (&$stagesExecuted) {
            $stagesExecuted[] = 'first-stage';
            return false;
        }));

        $this->object->addStage(new CallbackStage('second-stage', function (Workload\Task $task) use (&$stagesExecuted) {
            $stagesExecuted[] = 'second-stage';
        }));

        //execute
        $this->object->execute($workload, new Context());

        $this->assertCount(1, $stagesExecuted);
        $this->assertEquals('first-stage', $stagesExecuted[0]);
    }

    public function testExceptionCanHaltPipeline()
    {
        $stagesExecuted = array();

        //setup single task
        $workload = new Workload();
        $workload->addTask(new Workload\Task('foo'));

        //setup two stages
        $this->object->addStage(new CallbackStage('first-stage', function (Workload\Task $task) use (&$stagesExecuted) {
            $stagesExecuted[] = 'first-stage';
            throw new \RuntimeException('halts');
        }));

        $this->object->addStage(new CallbackStage('second-stage', function (Workload\Task $task) use (&$stagesExecuted) {
            $stagesExecuted[] = 'second-stage';
        }));

        //execute
        $this->object->execute($workload, new Context());

        $this->assertCount(1, $stagesExecuted);
        $this->assertEquals('first-stage', $stagesExecuted[0]);
    }


    public function testPrematureTaskDoneCanHaltPipeline()
    {
        $stagesExecuted = array();

        //setup single task
        $workload = new Workload();
        $workload->addTask(new Workload\Task('foo'));

        //setup two stages
        $this->object->addStage(new CallbackStage('first-stage', function (Workload\Task $task) use (&$stagesExecuted) {
            $stagesExecuted[] = 'first-stage';
            $task->setDone();
        }));

        $this->object->addStage(new CallbackStage('second-stage', function (Workload\Task $task) use (&$stagesExecuted) {
            $stagesExecuted[] = 'second-stage';
        }));

        //execute
        $this->object->execute($workload, new Context());

        $this->assertCount(1, $stagesExecuted);
        $this->assertEquals('first-stage', $stagesExecuted[0]);
    }

    public function testTaskFailedCanHaltPipeline()
    {
        $stagesExecuted = array();

        //setup single task
        $workload = new Workload();
        $workload->addTask(new Workload\Task('foo'));

        //setup two stages
        $this->object->addStage(new CallbackStage('first-stage', function (Workload\Task $task) use (&$stagesExecuted) {
            $stagesExecuted[] = 'first-stage';
            $task->setFailed();
        }));

        $this->object->addStage(new CallbackStage('second-stage', function (Workload\Task $task) use (&$stagesExecuted) {
            $stagesExecuted[] = 'second-stage';
        }));

        //execute
        $this->object->execute($workload, new Context());

        $this->assertCount(1, $stagesExecuted);
        $this->assertEquals('first-stage', $stagesExecuted[0]);
    }

    public function testExecuteReturnsTrueOnAllOk()
    {
        //setup single task
        $workload = new Workload();
        $workload->addTask(new Workload\Task('foo'));

        //setup two stages
        $this->object->addStage(new CallbackStage('first-stage', function (Workload\Task $task) {
            return true;
        }));

        $result = $this->object->execute($workload, new Context());
        $this->assertTrue($result);
    }

    public function testExecuteReturnsFalseOnAllFail()
    {
        //setup single task
        $workload = new Workload();
        $workload->addTask(new Workload\Task('foo'));

        //setup two stages
        $this->object->addStage(new CallbackStage('first-stage', function (Workload\Task $task) {
            $task->setFailed();
        }));

        $result = $this->object->execute($workload, new Context());
        $this->assertFalse($result);
    }

    public function testExecuteReturnsFalseOnSingleFail()
    {
        //setup single task
        $workload = new Workload();
        $workload->addTask(new Workload\Task('foo'));

        //setup two stages
        $this->object->addStage(new CallbackStage('first-stage', function (Workload\Task $task) {
            return true;
        }));

        $this->object->addStage(new CallbackStage('second-stage', function (Workload\Task $task) {
            $task->setFailed();
        }));

        $result = $this->object->execute($workload, new Context());
        $this->assertFalse($result);
    }

    public function testSetupCalledOnceForAllStagesAndTasks()
    {
        $workload = new Workload();
        $workload->addTask(new Workload\Task('foo'));
        $workload->addTask(new Workload\Task('bar'));

        $this->object->addStage(new CallbackStage('first-stage', function (Workload\Task $task) {
            return true;
        }));

        $this->object->addStage(new CallbackStage('second-stage', function (Workload\Task $task) {
            return true;
        }));

        $called = 0;
        $this->object->addSetup(function($workload, $context) use (&$called) {
            $called++;
        });

        $this->object->execute($workload, new Context());

        $this->assertEquals(1, $called);

    }

    public function testMultipleSetupsCalled()
    {
        $called = 0;
        $this->object->addSetup(function($workload, $context) use (&$called) {
            $called++;
        });

        $this->object->addSetup(function($workload, $context) use (&$called) {
            $called++;
        });

        $this->object->execute(new Workload(), new Context());

        $this->assertEquals(2, $called);
    }

    public function testWorkloadPassedToSetup()
    {
        $me = $this;
        $this->object->addSetup(function($workload, $context) use ($me) {
            $me->assertTrue($workload instanceof Workload);
        });
        $this->object->execute(new Workload(), new Context());
    }

    public function testContextPassedToSetup()
    {
        $me = $this;
        $this->object->addSetup(function($workload, $context) use ($me) {
            $me->assertTrue($context instanceof Context);
        });
        $this->object->execute(new Workload(), new Context());
    }

    public function testTeardownCalledOnceForAllStagesAndTasks()
    {
        $workload = new Workload();
        $workload->addTask(new Workload\Task('foo'));
        $workload->addTask(new Workload\Task('bar'));

        $this->object->addStage(new CallbackStage('first-stage', function (Workload\Task $task) {
                return true;
            }));

        $this->object->addStage(new CallbackStage('second-stage', function (Workload\Task $task) {
                return true;
            }));

        $called = 0;
        $this->object->addTeardown(function($workload, $context) use (&$called) {
                $called++;
            });

        $this->object->execute($workload, new Context());

        $this->assertEquals(1, $called);

    }

    public function testMultipleTeardownsCalled()
    {
        $called = 0;
        $this->object->addTeardown(function($workload, $context) use (&$called) {
            $called++;
        });

        $this->object->addTeardown(function($workload, $context) use (&$called) {
            $called++;
        });

        $this->object->execute(new Workload(), new Context());

        $this->assertEquals(2, $called);
    }

    public function testWorkloadPassedToTeardown()
    {
        $me = $this;
        $this->object->addTeardown(function($workload, $context) use ($me) {
            $me->assertTrue($workload instanceof Workload);
        });
        $this->object->execute(new Workload(), new Context());
    }

    public function testContextPassedToTeardown()
    {
        $me = $this;
        $this->object->addTeardown(function($workload, $context) use ($me) {
            $me->assertTrue($context instanceof Context);
        });
        $this->object->execute(new Workload(), new Context());
    }
}
