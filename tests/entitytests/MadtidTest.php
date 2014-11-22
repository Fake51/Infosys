<?php

require __DIR__ . '/../bootstrap.php';

class MadtidTest extends TestBase
{

    public $ef;

    public function setUp()
    {
        $this->ef = new EntityFactory(new DB);
    }

    public function testGetMad1()
    {
        $madtid = $this->ef->create('Madtider');
        $this->assertFalse($madtid->getMad());
    }

    public function testGetDeltagere1()
    {
        $madtid = $this->ef->create('Madtider');
        $this->assertTrue(is_array($madtid->getDeltagere()));
    }

    public function testGetFriendlyName()
    {
        $madtid = $this->ef->create('Madtider');
        $this->assertTrue(is_string($madtid->getFriendlyName()));
    }
}
