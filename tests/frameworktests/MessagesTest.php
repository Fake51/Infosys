<?php

require __DIR__ . '/../bootstrap.php';

class MessagesTest extends TestBase
{

    public function testGetAllMessagesAsText1()
    {
        $messages = new Messages($this->getSessionObject());
        $this->assertTrue(is_string($messages->getAllMessagesAsText()));
    }

    public function testGetAllMessagesAsHtml1()
    {
        $messages = new Messages($this->getSessionObject());
        $this->assertTrue(is_string($messages->getAllMessagesAsHtml()));
    }

    public function testGetAllMessagesRaw1()
    {
        $messages = new Messages($this->getSessionObject());
        $this->assertTrue(is_string($messages->getAllMessagesRaw()));
    }

    public function testAddError()
    {
        $messages = new Messages($this->getSessionObject());
        $this->assertTrue($messages->addError('test'));
    }

    public function testAddSuccess()
    {
        $messages = new Messages($this->getSessionObject());
        $this->assertTrue($messages->addSuccess('test'));
    }

    public function testGetAllMessagesAsText2()
    {
        $messages = new Messages($this->getSessionObject());
        $this->assertTrue($messages->addSuccess('test'));
        $return = $messages->getAllMessagesAsText();
        $this->assertTrue(strpos($return, 'Successes:') !== false);
        $this->assertTrue(strpos($return, 'test') !== false);
        $return = $messages->getAllMessagesAsText();
        $this->assertFalse(strpos($return, 'Successes:') !== false);
        $this->assertFalse(strpos($return, 'test') !== false);
    }

    public function testGetAllMessagesAsHtml2()
    {
        $messages = new Messages($this->getSessionObject());
        $this->assertTrue($messages->addSuccess('test'));
        $return = $messages->getAllMessagesAsHtml();
        $this->assertTrue(strpos($return, 'successes') !== false);
        $this->assertTrue(strpos($return, 'test') !== false);
        $return = $messages->getAllMessagesAsHtml();
        $this->assertFalse(strpos($return, 'Successes:') !== false);
        $this->assertFalse(strpos($return, 'test') !== false);
    }

    public function testGetAllMessagesRaw2()
    {
        $messages = new Messages($this->getSessionObject());
        $this->assertTrue($messages->addSuccess('test'));
        $return = $messages->getAllMessagesRaw();
        $this->assertTrue(strpos($return, 'Successes:') !== false);
        $this->assertTrue(strpos($return, 'test') !== false);
        $return = $messages->getAllMessagesRaw();
        $this->assertFalse(strpos($return, 'Successes:') !== false);
        $this->assertFalse(strpos($return, 'test') !== false);
    }

    public function getSessionObject()
    {
        $session = $this->getMock('Session', array('__construct', '__get', '__set', 'delete'));
        $session->expects($this->any())
            ->method('__get')
            ->will($this->returnValue(array('errors' => array(), 'successes' => array())));
        return $session;
    }
}
