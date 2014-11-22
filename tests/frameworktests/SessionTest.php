<?php

require __DIR__ . '/../bootstrap.php';

class SessionTest extends TestBase
{
    public function setUp()
    {
        if (!session_id())
        {
            $_SESSION = null;
            session_destroy();
            unset($_SESSION);
        }
    }

    public function testGetState()
    {
        $session = new Session;
        $this->assertTrue($session->getState());
    }

    public function testEmptyGet()
    {
        $session = new Session;
        $this->assertTrue(is_null($session->blah));
    }

    public function testFalseIsset()
    {
        $session = new Session;
        $this->assertFalse(isset($session->blah));
    }

    public function testSave()
    {
        $session = new Session;
        $session->save();
    }

    public function testSet()
    {
        $session = new Session;
        $session->blah = "test";
        $this->assertTrue(isset($_SESSION['blah']));

        $session2 = new Session;
        $this->assertTrue($session2->blah == 'test');

        $session3 = new Session;
        $this->assertTrue(isset($session3->blah));

        $session4 = new Session;
        $this->assertTrue($session4->delete('blah'));
    }

    public function testBadDelete()
    {
        $session = new Session;
        $this->assertFalse($session->delete('test'));
    }

    public function testEnd()
    {
        $session = new Session;
        try
        {
            $session->end();
        }
        catch (Exception $e)
        {
            if (strpos($e->getMessage(), 'Cannot modify header information') === false) throw new Exception('failed');
        }
    }
}
