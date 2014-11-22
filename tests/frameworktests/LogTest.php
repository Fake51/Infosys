<?php

require __DIR__ . '/../bootstrap.php';

class LogTest extends TestBase
{

    public function testLogToFile1()
    {
        $log = new Log($this->getDB());
        $this->assertTrue($log->logToFile('test message'));
    }

    public function testLogToFile2()
    {
        $log = new Log($this->getDB());
        $this->setExpectedException('FrameworkException');
        $this->assertTrue($log->logToFile(''));
    }

    public function testLogToDB()
    {
        $log = new Log($this->getDB());
        $this->assertTrue($log->logToDB('test message', 'test', 1));
    }

    public function getDB()
    {
        $db = $this->getMock('DB', array('exec'));
        $db->expects($this->any())
            ->method('exec')
            ->will($this->returnValue(true));
        return $db;
    }
}
