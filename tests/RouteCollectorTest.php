<?php

use PHPUnit\Framework\TestCase;

class RouteCollectorTest extends TestCase
{

    protected $dataGenerator;

    protected $routeParser;

    public function setUp()
    {
        $this->routeParser = Mockery::mock(\FastRoute\RouteParser::class);
        $this->dataGenerator = Mockery::mock(\FastRoute\DataGenerator::class);
    }

    /**
     * @test
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function canAddRoute()
    {
        $collector = new \Venta\Routing\RouteCollector($this->routeParser, $this->dataGenerator);
        $collector->addRoute(['GET'], '/url', 'handle');
        $route = $collector->getRoutes()[0];
        $this->assertInstanceOf(\Venta\Routing\Route::class, $route);
        $this->assertSame(['GET'], $route->getMethods());
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handle', $route->getCallable());
    }

    /**
     * @test
     */
    public function canAddRouteInstance()
    {
        $route = new \Venta\Routing\Route(['GET'], '/abc', 'callable');
        $collector = new \Venta\Routing\RouteCollector($this->routeParser, $this->dataGenerator);
        $collector->add($route);
        $this->assertCount(1, $collector->getRoutes());
        $this->assertContains($route, $collector->getRoutes());
        $collector->add($route->withName('named'));
        $this->assertCount(2, $collector->getRoutes());
        $this->assertArrayHasKey('named', $collector->getRoutes());
    }

    /**
     * @test
     */
    public function canGenerateUrl()
    {
        $route = Mockery::mock(\Venta\Routing\Route::class);
        $route->shouldReceive('getName')->withNoArgs()->andReturn('route');
        $route->shouldReceive('url')->with(['param' => 'value'])->andReturn('/url/value')->once();
        $collector = new \Venta\Routing\RouteCollector($this->routeParser, $this->dataGenerator);
        $collector->add($route);
        $url = $collector->url('route', ['param' => 'value']);
        $this->assertSame('/url/value', $url);
    }

    /**
     * @test
     */
    public function canGetData()
    {
        $route = new \Venta\Routing\Route(['GET'], '/abc', 'callable');
        $collector = new \Venta\Routing\RouteCollector($this->routeParser, $this->dataGenerator);
        $this->routeParser->shouldReceive('parse')->with('/abc')->andReturn(['route data'])->once();
        $this->dataGenerator->shouldReceive('addRoute')->with('GET', 'route data', $route)->once();
        $this->dataGenerator->shouldReceive('getData')->withNoArgs()->andReturn(['parsed data']);
        $collector->add($route);
        $data = $collector->getData();
        $this->assertSame(['parsed data'], $data);
    }

    /**
     * @test
     */
    public function canGetFilteredData()
    {
        $collector = new \Venta\Routing\RouteCollector($this->routeParser, $this->dataGenerator);
        // this one will be passed to parent::addRote
        $collector->add(new \Venta\Routing\Route(['GET'], '/abc', 'callable'));
        // this one will be rejected by host
        $collector->add((new \Venta\Routing\Route(['GET'], '/abc', 'callable'))->withHost('127.0.0.1'));
        // this one will be rejected by scheme
        $collector->add((new \Venta\Routing\Route(['GET'], '/abc', 'callable'))->withScheme('https'));
        // this one will be passed to parent::addRote
        $collector->add((new \Venta\Routing\Route(['GET'], '/def',
            'callable'))->withScheme('http')->withHost('localhost'));
        $this->routeParser->shouldReceive('parse')->with('/abc')->andReturn(['route data'])->once();
        $this->routeParser->shouldReceive('parse')->with('/def')->andReturn(['route data'])->once();
        $this->dataGenerator->shouldReceive('addRoute')
                            ->with('GET', 'route data', Mockery::type(\Venta\Routing\Route::class))
                            ->twice();
        $this->dataGenerator->shouldReceive('getData')->withNoArgs()->andReturn(['parsed data']);
        $request = Mockery::mock(\Psr\Http\Message\RequestInterface::class);
        $uri = Mockery::mock(\Psr\Http\Message\UriInterface::class);
        $uri->shouldReceive('getHost')->andReturn('localhost');
        $uri->shouldReceive('getScheme')->andReturn('http');
        $request->shouldReceive('getUri')->andReturn($uri);
        $data = $collector->getFilteredData($request);
        $this->assertSame(['parsed data'], $data);
    }

    /**
     * @test
     */
    public function canGroup()
    {
        $collector = new \Venta\Routing\RouteCollector($this->routeParser, $this->dataGenerator);
        $route = new \Venta\Routing\Route(['GET'], 'url', 'handle');
        $collector->group('prefix', function (\Venta\Contracts\Routing\RouteGroup $collector) use ($route) {
            // host and scheme must be set before ->group() call
            // to affect routes inside new group
            $collector->setHost('localhost');

            // test group creation inside group
            $collector->group('more', function (\Venta\Contracts\Routing\RouteGroup $c) use ($route) {
                $c->setScheme('https');

                // route must have scheme from this group
                // and host from the outer one
                $c->add($route);
            });
        });
        $this->assertCount(1, $collector->getRoutes());
        $r = $collector->getRoutes()[0];
        $this->assertSame('/prefix/more/url', $r->getPath());
        $this->assertSame('https', $r->getScheme());
        $this->assertSame('localhost', $r->getHost());
    }

    /**
     * @test
     */
    public function throwsExceptionIfRouteIsNotFound()
    {
        $this->expectException(InvalidArgumentException::class);

        $collector = new \Venta\Routing\RouteCollector($this->routeParser, $this->dataGenerator);
        $collector->url('non-existing route');
    }

}
