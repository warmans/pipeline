<?php
namespace Pipeline;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Context
     */
    private $object;

    public function setUp()
    {
        $this->object = new Context();
    }

    public function testSetLoggerIsInvoked()
    {
        $messagesLogged = array();
        $this->object->setLogger(function($msg, $writeln=true) use (&$messagesLogged) {
            $messagesLogged[] = $msg;
        });

        $this->object->log('foo');

        $this->assertEquals('foo', $messagesLogged[0]);
    }

    public function testPushSinglePrefix()
    {
        $messagesLogged = array();
        $this->object->setLogger(function($msg, $writeln=true) use (&$messagesLogged) {
            $messagesLogged[] = $msg;
        });

        $this->object->pushPrefix('>');
        $this->object->log('foo');

        $this->assertEquals('> foo', $messagesLogged[0]);
    }

    public function testPushMultiplePrefix()
    {
        $messagesLogged = array();
        $this->object->setLogger(function($msg, $writeln=true) use (&$messagesLogged) {
            $messagesLogged[] = $msg;
        });

        $this->object->pushPrefix('>');
        $this->object->pushPrefix('>');
        $this->object->pushPrefix('>');
        $this->object->log('foo');

        $this->assertEquals('> > > foo', $messagesLogged[0]);
    }

    public function testPopPrefix()
    {
        $messagesLogged = array();
        $this->object->setLogger(function($msg, $writeln=true) use (&$messagesLogged) {
            $messagesLogged[] = $msg;
        });

        //add 3 prefixes
        $this->object->pushPrefix('a');
        $this->object->pushPrefix('b');
        $this->object->pushPrefix('c');

        //pop off 2
        $this->object->popPrefix();
        $this->object->popPrefix();

        //log something
        $this->object->log('foo');

        //we should see only the first prefix was used
        $this->assertEquals('a foo', $messagesLogged[0]);
    }

    public function testSetGetData()
    {
        $this->object->setData('foo', 'bar');
        $this->assertEquals($this->object->getData('foo'), 'bar');
    }

}
 