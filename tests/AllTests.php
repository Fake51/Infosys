<?php

require __DIR__ . '/bootstrap.php';

class AllTests extends SuiteBase
{
    protected static $folder = '.';

    public static function suite()
    {
        return parent::init();
    }
}

