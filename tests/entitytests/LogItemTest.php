<?php

require __DIR__ . '/../bootstrap.php';

class LogItemTest extends TestBase
{
    private $ef;

    public function setup()
    {
        $db = new DB;
        $this->ef = new EntityFactory($db);
    }

    public function testClass()
    {
        $log = $this->ef->create('LogItem');
        $this->assertTrue($log instanceof LogItem);
        $this->assertTrue($log instanceof DbObject);
        $this->assertFalse($log->isLoaded());
    }

    public function testGetTypes()
    {
        $log = $this->ef->create('LogItem');
        $this->assertTrue(is_array($log->getTypes()));
    }

    public function testSearchLogs1()
    {
        $log = $this->ef->create('LogItem');
        $this->setExpectedException('Exception');
        $log->searchLogs();
    }

    public function testSearchLogs2()
    {
        $log = $this->ef->create('LogItem');
        $this->setExpectedException('Exception');
        $log->searchLogs(false);
    }

    public function testSearchLogs3()
    {
        $log = $this->ef->create('LogItem');
        $this->setExpectedException('Exception');
        $log->searchLogs('hej');
    }

    public function testSearchLogs4()
    {
        $log = $this->ef->create('LogItem');
        $this->assertTrue(is_array($result = $log->searchLogs(new StdClass)));
        $this->assertTrue(count($result) <= 20);
    }

    public function testSearchLogs5()
    {
        $log = $this->ef->create('LogItem');
        $obj->search_term = 'logged';
        $this->assertTrue(is_array($result = $log->searchLogs($obj)));
        $this->assertTrue(count($result) <= 20 && count($result) > 0);

        $obj->search_term = 'logged in';
        $this->assertTrue(is_array($result = $log->searchLogs($obj)));
        $this->assertTrue(count($result) <= 20 && count($result) > 0);
    }

    public function testSearchLogs6()
    {
        $log = $this->ef->create('LogItem');
        $obj->category = 'Login';
        $this->assertTrue(is_array($result = $log->searchLogs($obj)));
        $this->assertTrue(count($result) <= 20 && count($result) > 0);
    }

    public function testSearchLogs7()
    {
        $log = $this->ef->create('LogItem');
        $obj->user = $this->ef->create('User')->findById(1);
        $this->assertTrue(is_array($result = $log->searchLogs($obj)));
        $this->assertTrue(count($result) <= 20 && count($result) > 0);
    }

    public function testSearchLogs8()
    {
        $log = $this->ef->create('LogItem');
        $obj->search_term = 'logged';
        $this->assertTrue(is_array($result = $log->searchLogs($obj)));
        $this->assertTrue(count($result) <= 20 && count($result) > 0);

        $array = array();
        foreach ($result as $res)
        {
            $array[] = $res->id;
        }

        $obj->page = 1;
        $this->assertTrue(is_array($result = $log->searchLogs($obj)));
        $this->assertTrue(count($result) <= 20 && count($result) > 0);

        foreach ($result as $res)
        {
            $this->assertTrue(!in_array($res->id, $array));
        }
    }
}
