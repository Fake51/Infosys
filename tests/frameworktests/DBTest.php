<?php

require __DIR__ . '/../bootstrap.php';

class DBTest extends TestBase
{
    public function testGetState()
    {
        $db = new DB;
        $this->assertTrue($db->getState());
    }

    public function testDestroy()
    {
        $db = new DB;
        $db->destroy();
    }

    public function sanitize()
    {
        $db = new DB;
        $test = "sanitize ' this\x00";
        $output = $db->sanitize($this);
        $this->assertTrue(mysql_real_escape_string($test) == $output);
    }

    public function testBegin()
    {
        $db = new DB;
        $db->begin();
        $db->begin();
        $db->begin();
        $db->begin();
    }

    public function testCommit1()
    {
        $db = new DB;
        $this->setExpectedException('DBException');
        $db->commit();
    }

    public function testCommit2()
    {
        $db = new DB;
        $db->begin();
        $db->commit();
    }

    public function testCommit3()
    {
        $db = new DB;
        $db->begin();
        $db->commit();
        $this->setExpectedException('DBException');
        $db->rollback();
    }

    public function testRollback1()
    {
        $db = new DB;
        $this->setExpectedException('DBException');
        $db->rollback();
    }

    public function testRollback2()
    {
        $db = new DB;
        $db->begin();
        $db->rollback();
    }

    public function testRollback3()
    {
        $db = new DB;
        $db->begin();
        $db->rollback();
        $this->setExpectedException('DBException');
        $db->commit();
    }

    public function testGetTransactionState()
    {
        $db = new DB;
        $this->assertFalse($db->getTransactionState());
        $db->begin();
        $this->assertTrue($db->getTransactionState());
        $db->rollback();
        $this->assertFalse($db->getTransactionState());
    }

    public function testQuery1()
    {
        $db = new DB;
        $this->setExpectedException('DBException');
        $db->query("update");
    }

    public function testQuery2()
    {
        $db = new DB;
        $this->setExpectedException('DBException');
        $db->query("DELETE");
    }

    public function testQuery3()
    {
        $db = new DB;
        $this->setExpectedException('DBException');
        $db->query("InSeRT");
    }

    public function testQuery4()
    {
        $db = new DB;
        $result = $db->query("select 1 as number");
        $this->assertTrue(is_array($result));
        $this->assertTrue(isset($result[0]) && $result[0]['number'] == 1);
    }

}
