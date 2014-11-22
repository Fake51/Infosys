<?php

require __DIR__ . '/../bootstrap.php';

class RequestTest extends TestBase
{

    public function tearDown()
    {
        unset($_POST, $_GET, $_SERVER['REQUEST_METHOD']);
    }

    public function testInit()
    {
        $routes = $this->getMock('Routes');
        $request = new Request($routes);
    }

    public function testIsPost1()
    {
        $routes = $this->getMock('Routes');
        $request = new Request($routes);
        $this->assertFalse($request->isPost());
    }

    public function testPostObject1()
    {
        $routes = $this->getMock('Routes');
        $request = new Request($routes);
        $this->assertTrue(is_null($request->post));
    }

    public function testIsPost2()
    {
        $_POST = array();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $routes = $this->getMock('Routes');
        $request = new Request($routes);
        $this->assertTrue($request->isPost());
    }

    public function testIsGet1()
    {
        $routes = $this->getMock('Routes');
        $request = new Request($routes);
        $this->assertFalse($request->isGet());
    }

    public function testGetObject1()
    {
        $routes = $this->getMock('Routes');
        $request = new Request($routes);
        $this->assertTrue($request->get === null);
    }

    public function testIsGet2()
    {
        $_GET = array();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $routes = $this->getMock('Routes');
        $request = new Request($routes);
        $this->assertTrue($request->isGet());
    }

    public function testPostObject2()
    {
        $routes = $this->getMock('Routes');
        $request = new Request($routes);
        $this->assertTrue($request->post instanceof RequestVars);
    }

    public function testGetObject2()
    {
        $routes = $this->getMock('Routes');
        $request = new Request($routes);
        $this->assertTrue($request->get instanceof RequestVars);
    }

    public function testBadGet()
    {
        $routes = $this->getMock('Routes');
        $request = new Request($routes);
        $this->assertTrue(is_null($request->blah));
    }

    public function testGetRoutes()
    {
        $routes = $this->getMock('Routes');
        $request = new Request($routes);
        $this->assertTrue($request->routes instanceof Routes);
    }

    public function testGetPath()
    {
        $routes = $this->getMock('Routes');
        $request = new Request($routes);
        $this->assertTrue(is_string($request->getPath()));
        $this->assertTrue($request->getPath() == '');
    }

    public function testGetRoute()
    {
        $routes = $this->getMock('Routes', array('matchRoute'));
        $routes->expects($this->once())
            ->method('matchRoute')
            ->with($this->equalTo(''))
            ->will($this->returnValue(array()));

        $request = new Request($routes);
        $this->assertTrue(is_array($request->getRoute()));
    }

    public function testGetServer()
    {
        $routes = $this->getMock('Routes');
        $request = new Request($routes);
        $this->assertTrue($request->server instanceof RequestVars);
        $this->assertFalse(isset($request->server->REQUEST_URI));
        $this->assertTrue(isset($request->server->REQUEST_METHOD));
    }
}
