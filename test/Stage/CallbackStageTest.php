<?php
namespace Pipeline\Stage;

use Pipeline\Context;
use Pipeline\Workload\Task;

class CallbackStageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CallbackStage
     */
    private $object;

    public function testExecutesCallback()
    {
        $executed = false;
        $this->object = new CallbackStage('foo', function () use (&$executed) {
            $executed = true;
        });

        $this->object->execute(new Task('foo'), new Context());
        $this->assertTrue($executed);
    }

    public function testGetNameReturnsSuppliedName()
    {
        $this->object = new CallbackStage('foo', function () use (&$executed) {
            return true;
        });

        $this->assertEquals('foo', $this->object->getName());
    }
}
