<?php

class TestBase extends PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        $this->backupGlobals = false;
        //$this->backupStaticAttributes = false;
    }
}
