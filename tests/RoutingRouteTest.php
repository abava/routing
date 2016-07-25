<?php

class RoutingRouteTest extends PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $route = new \Abava\Routing\Route(['GET', 'POST'], '/uri', 'controller@action');
        $this->assertSame(['GET', 'POST'], $route->getMethods());
        $this->assertSame('/uri', $route->getPath());
        $this->assertSame('controller@action', $route->getCallable());
    }

    public function testImmutability()
    {
        // creating basic route
        $route = new \Abava\Routing\Route(['GET', 'POST'], '/uri', 'controller@action');
        $this->assertSame(['GET', 'POST'], $route->getMethods());
        $this->assertSame('/uri', $route->getPath());
        $this->assertSame('controller@action', $route->getCallable());

        // testing host immutability
        $new = $route->withHost('localhost');
        $this->assertSame('localhost', $new->getHost());
        $this->assertSame('', $route->getHost());
        $this->assertNotSame($route->getHost(), $new->getHost());
        $this->assertNotSame($route, $new);

        // testing scheme immutability
        $new = $route->withScheme('http');
        $this->assertSame('http', $new->getScheme());
        $this->assertSame('', $route->getScheme());
        $this->assertNotSame($route->getScheme(), $new->getScheme());
        $this->assertNotSame($route, $new);

        // testing name immutability
        $new = $route->withName('named');
        $this->assertSame('named', $new->getName());
        $this->assertSame('', $route->getName());
        $this->assertNotSame($route->getName(), $new->getName());
        $this->assertNotSame($route, $new);

        // testing path (url) immutability
        $new = $route->withPath('/url');
        $this->assertSame('/url', $new->getPath());
        $this->assertSame('/uri', $route->getPath());
        $this->assertNotSame($route->getPath(), $new->getPath());
        $this->assertNotSame($route, $new);

        // testing parameter immutability
        $new = $route->withParameters(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $new->getParameters());
        $this->assertSame([], $route->getParameters());
        $this->assertNotSame($route->getParameters(), $new->getParameters());
        $this->assertNotSame($route, $new);

        // testing middleware immutability
        $closure = function(){};
        $new = $route->withMiddleware('middleware', $closure);
        $this->assertSame(['middleware' => $closure], $new->getMiddlewares());
        $this->assertSame([], $route->getMiddlewares());
        $this->assertNotSame($route->getMiddlewares(), $new->getMiddlewares());
        $this->assertNotSame($route, $new);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Middleware must either implement Middleware contract or be callable
     */
    public function testPassingInvalidMiddleware()
    {
        // creating basic route
        $route = new \Abava\Routing\Route(['GET', 'POST'], '/uri', 'controller@action');
        $route->withMiddleware('invalid', 'middleware');
    }

}
