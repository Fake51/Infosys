<?php

require __DIR__ . '/../bootstrap.php';

class ViewhelperTest extends TestBase
{

    public function testGetTimeArray()
    {
        $vh = new ViewHelper();
        $res = $vh->getTimeArray();
        $this->assertTrue(is_array($res));
        foreach (array_flip($res) as $idx => $val)
        {
            $this->assertTrue(sprintf('%02d:00', $idx) === $val);
        }
    }
}
