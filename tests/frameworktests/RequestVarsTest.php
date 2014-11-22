<?php

require __DIR__ . '/../bootstrap.php';

class RequestVarsTest extends TestBase
{

    public function testInit()
    {
        $rv = new RequestVars(array());
    }

    public function testGetVars()
    {
        $rv = new RequestVars($this->arrayVars());
        $this->assertTrue($rv->var1 == 'test1');
        $this->assertTrue($rv->var2 == 'test2');
        $this->assertTrue($rv->var3 == 'test3');
        $this->assertTrue(is_array($rv->var4));
        $this->assertTrue(is_float($rv->var5));
        $this->assertTrue(is_null($rv->bad));
    }

    public function testIssetVars()
    {
        $rv = new RequestVars($this->arrayVars());
        $this->assertTrue(isset($rv->var1));
        $this->assertTrue(isset($rv->var2));
        $this->assertTrue(isset($rv->var3));
        $this->assertTrue(isset($rv->var4));
        $this->assertTrue(isset($rv->var5));
        $this->assertTrue(!isset($rv->bad));
    }

    public function testGetRequestVarArray()
    {
        $rv = new RequestVars($this->arrayVars());
        $this->assertTrue(is_array($rv->getRequestVarArray()));
        $array = $rv->getRequestVarArray();
        $this->assertTrue(isset($array['var1']));
        $this->assertTrue(isset($array['var2']));
        $this->assertTrue(isset($array['var3']));
        $this->assertTrue(isset($array['var4']));
        $this->assertTrue(isset($array['var5']));
        $this->assertTrue(!isset($array['var6']));
    }

    public function arrayVars()
    {
        return array(
            'var1'  => "test1", 
            'var2'  => "test2", 
            'var3'  => "test3", 
            'var4'  => array(1, 2, 3),
            'var5'  => 1.453224,
        );
    }
}
