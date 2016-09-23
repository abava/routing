<?php

use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    /**
     * @test
     */
    public function testConstruct()
    {
        $route = new \Venta\Routing\Route(['GET', 'POST'], '/uri', 'controller@action');
        $this->assertSame(['GET', 'POST'], $route->getMethods());
        $this->assertSame('/uri', $route->getPath());
        $this->assertSame('controller@action', $route->getCallable());
    }

    /**
     * @test
     */
    public function testImmutability()
    {
        // creating basic route
        $route = new \Venta\Routing\Route(['GET', 'POST'], '/uri', 'controller@action');
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
        $closure = function () {
        };
        $new = $route->withMiddleware('middleware', $closure);
        $this->assertSame(['middleware' => $closure], $new->getMiddlewares());
        $this->assertSame([], $route->getMiddlewares());
        $this->assertNotSame($route->getMiddlewares(), $new->getMiddlewares());
        $this->assertNotSame($route, $new);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Middleware must either implement Middleware contract or be callable
     * @test
     */
    public function testPassingInvalidMiddleware()
    {
        // creating basic route
        $route = new \Venta\Routing\Route(['GET', 'POST'], '/uri', 'controller@action');
        $route->withMiddleware('invalid', 'middleware');
    }

    /**
     * @test
     */
    public function testUrlBuild()
    {
        $route = new \Venta\Routing\Route(['GET'], '/url/{id}', 'handle');
        $this->assertSame('/url/123', $route->url(['id' => 123]), 'common parameter replaced');

        $route = new \Venta\Routing\Route(['GET'], '/url/{id:[0-9]+}', 'handle');
        $this->assertSame('/url/123', $route->url(['id' => 123]), 'parameter with pattern replaced');

        $route = new \Venta\Routing\Route(['GET'], '/url/{id:number}', 'handle');
        $this->assertSame('/url/123', $route->url(['id' => 123]), 'parameter with named pattern replaced');

        $route = new \Venta\Routing\Route(['GET'], '/url[/{id:number}[/{name}]]', 'handle');
        $this->assertSame('/url/123', $route->url(['id' => 123]), 'optional parameter with named pattern replaced');

        $route = new \Venta\Routing\Route(['GET'], '/url[/{id:number}[//{name}]]', 'handle');
        $this->assertSame('/url/123', $route->url(['id' => 123]), 'optional parameter with named pattern replaced');

        $route = new \Venta\Routing\Route(['GET'], '/url[/{id:number}[/{name}]]', 'handle');
        $this->assertSame('/url/123/a', $route->url(['id' => 123, 'name' => 'a']), 'all optional parameters replaced');

        $route = new \Venta\Routing\Route(['GET'], '/url[/{one:number}[/{two}[/{three:[0-9]+}[/{four}]]]]', 'handle');
        $this->assertSame('/url', $route->url(), 'none optional parameters replaced');
    }

    /**
     * @test
     */
    public function testUrlBuildNonOptionalParameterIsNotProvided()
    {
        $this->expectException(InvalidArgumentException::class);
        $route = new \Venta\Routing\Route(['GET'], '/url/{id:number}[/{optional}]', 'handle');
        $route->url();
    }

    /**
     * @test
     */
    public function testUrlBuildOptionalParametersPassed()
    {
        $this->expectException(InvalidArgumentException::class);
        $route = new \Venta\Routing\Route(['GET'], '/url[/{one}[/{two}[/{three}]]]', 'handle');
        $route->url(['two' => 'abc']);
    }

    /**
     * @test
     */
    public function testUrlBuildParameterDoesntMatchPattern()
    {
        $this->expectException(InvalidArgumentException::class);
        $route = new \Venta\Routing\Route(['GET'], '/url/{id:number}', 'handle');
        $route->url(['id' => 'abc']);
    }

}
