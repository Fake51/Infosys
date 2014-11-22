<?php

require __DIR__ . '/bootstrap.php';

class ModelTests extends SuiteBase
{
    protected static $folder = 'modeltests';

    public static function suite()
    {
        return parent::init();
    }
}

