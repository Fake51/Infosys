<?php

require __DIR__ . '/../bootstrap.php';

class LogModelTest extends TestBase
{
    public function testGetLogMessages1()
    {
        $log = new LogModel(new DB);
        $this->assertTrue(is_array($log->getLogMessages()));
    }

    public function testGetLogMessages2()
    {
        $log = new LogModel(new DB);
        $this->assertTrue(is_array($log->getLogMessages(false)));
    }

    public function testGetLogMessages3()
    {
        $log = new LogModel(new DB);
        $this->assertTrue(is_array($log->getLogMessages(false, 100)));
    }

    public function testGetLogMessages4()
    {
        $log = new LogModel(new DB);
        $this->assertTrue(is_array($log->getLogMessages(false, -100)));
    }

    public function testGetLogMessages5()
    {
        $log = new LogModel(new DB);
        $this->assertTrue(is_array($log->getLogMessages(true, -100, 100)));
    }

    public function testGetLogMessages6()
    {
        $log = new LogModel(new DB);
        $this->assertTrue(is_array($log->getLogMessages(true, 100, 100)));
    }

    public function testGetLogMessages7()
    {
        $log = new LogModel(new DB);
        $this->assertTrue(is_array($log->getLogMessages(true, 100, 1000000)));
    }

    public function testGetLogMessages8()
    {
        $log = new LogModel(new DB);
        $this->assertTrue(is_array($log->getLogMessages(true, 100000000, 1000)));
    }

    public function testGetLogMessages9()
    {
        $log = new LogModel(new DB);
        $this->assertTrue(is_array($log->getLogMessages(true, 100000000, -1000)));
    }

    public function testGetPaged1()
    {
        $log = new LogModel(new DB);
        $this->assertTrue(is_array($log->getPaged(0, 0)));
    }

    public function testGetPaged2()
    {
        $log = new LogModel(new DB);
        $this->assertTrue(is_array($log->getPaged(-1, 0)));
    }

    public function testGetPaged3()
    {
        $log = new LogModel(new DB);
        $this->assertTrue(is_array($log->getPaged(0, -1)));
    }

    public function testGetPaged4()
    {
        $log = new LogModel(new DB);
        $this->assertTrue(is_array($log->getPaged(-1, -1)));
    }

    public function testGetPaged5()
    {
        $log = new LogModel(new DB);
        $this->assertTrue(is_array($log->getPaged(10000000, 10000000)));
    }

    public function testGetLogTypes()
    {
        $log = new LogModel(new DB);
        $this->assertTrue(is_array($log->getLogTypes()));
    }

    public function testGetUsers()
    {
        $log = new LogModel(new DB);
        $this->assertTrue(is_array($log->getUsers()));
    }
}
