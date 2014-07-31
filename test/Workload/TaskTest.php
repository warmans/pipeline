<?php
/**
 * Created by PhpStorm.
 * User: swarman
 * Date: 31/07/14
 * Time: 13:36
 */

namespace Pipeline\Workload;


class TaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Task
     */
    private $object;

    public function setUp()
    {
        $this->object = new Task('foo', array('bar'=>'baz'));
    }

    public function testGetLabel()
    {
        $this->assertEquals('foo', $this->object->getLabel());
    }

    public function testNotDoneByDefault()
    {
        $this->assertFalse($this->object->isDone());
    }

    public function testSetDone()
    {
        $this->object->setDone();
        $this->assertTrue($this->object->isDone());
    }

    public function testNotFailedbyDefault()
    {
        $this->assertFalse($this->object->isFailed());
    }

    public function testSetFailed()
    {
        $this->object->setFailed();
        $this->assertTrue($this->object->isFailed());
    }

    public function testSetFailedMessageGetErrors()
    {
        $this->object->setFailed('no reason');
        $errors = $this->object->getErrors();

        $this->assertEquals('no reason', $errors[0]);
    }

    public function testSetGetNewMeta()
    {
        $this->object->setMeta('cat', 'dog');
        $this->assertEquals('dog', $this->object->getMeta('cat'));
    }

    public function testUpdateGetMeta()
    {
        $this->object->setMeta('bar', 'dog');
        $this->assertEquals('dog', $this->object->getMeta('bar'));
    }

    public function testMetaViaConstructor()
    {
        $this->assertEquals('baz', $this->object->getMeta('bar'));
    }
}
 