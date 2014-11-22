<?php

require __DIR__ . '/bootstrap.php';

class FrameworkTests extends SuiteBase
{

    protected static $folder = 'frameworktests';

    public static function suite()
    {
        return parent::init();
    }
}

