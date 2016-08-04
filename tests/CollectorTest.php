<?php

use PHPUnit\Framework\TestCase;

/**
 * Class CollectorTest
 */
class CollectorTest extends TestCase
{

    protected $routeParser;
    protected $dataGenerator;

    public function setUp()
    {
        $this->routeParser = Mockery::mock(\FastRoute\RouteParser::class);
        $this->dataGenerator = Mockery::mock(\FastRoute\DataGenerator::class);
    }

    /**
     * @test
     */
    public function canAddRoute()
    {
        $collector = new \Abava\Routing\Collector($this->routeParser, $this->dataGenerator);
        $collector->addRoute(['GET'], '/url', 'handle');
        $route = $collector->getRoutes()[0];
        $this->assertInstanceOf(\Abava\Routing\Route::class, $route);
        $this->assertSame(['GET'], $route->getMethods());
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handle', $route->getCallable());
    }

    /**
     * @test
     */
    public function canAddRouteInstance()
    {
        $route = new \Abava\Routing\Route(['GET'], '/abc', 'callable');
        $collector = new \Abava\Routing\Collector($this->routeParser, $this->dataGenerator);
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
    public function canGroup()
    {
        $collector = new \Abava\Routing\Collector($this->routeParser, $this->dataGenerator);
        $route = new \Abava\Routing\Route(['GET'], 'url', 'handle');
        $collector->group('prefix', function (\Abava\Routing\Contract\Group $collector) use ($route) {
            // host and scheme must be set before ->group() call
            // to affect routes inside new group
            $collector->setHost('localhost');

            // test group creation inside group
            $collector->group('more', function (\Abava\Routing\Contract\Group $c) use ($route) {
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
    public function canGetData()
    {
        $route = new \Abava\Routing\Route(['GET'], '/abc', 'callable');
        $collector = new \Abava\Routing\Collector($this->routeParser, $this->dataGenerator);
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
        $collector = new \Abava\Routing\Collector($this->routeParser, $this->dataGenerator);
        // this one will be passed to parent::addRote
        $collector->add(new \Abava\Routing\Route(['GET'], '/abc', 'callable'));
        // this one will be rejected by host
        $collector->add((new \Abava\Routing\Route(['GET'], '/abc', 'callable'))->withHost('127.0.0.1'));
        // this one will be rejected by scheme
        $collector->add((new \Abava\Routing\Route(['GET'], '/abc', 'callable'))->withScheme('https'));
        // this one will be passed to parent::addRote
        $collector->add((new \Abava\Routing\Route(['GET'], '/def',
            'callable'))->withScheme('http')->withHost('localhost'));
        $this->routeParser->shouldReceive('parse')->with('/abc')->andReturn(['route data'])->once();
        $this->routeParser->shouldReceive('parse')->with('/def')->andReturn(['route data'])->once();
        $this->dataGenerator->shouldReceive('addRoute')
            ->with('GET', 'route data', Mockery::type(\Abava\Routing\Route::class))
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
    public function canGenerateUrl()
    {
        $route = Mockery::mock(\Abava\Routing\Route::class);
        $route->shouldReceive('getName')->withNoArgs()->andReturn('route');
        $route->shouldReceive('url')->with(['param' => 'value'])->andReturn('/url/value')->once();
        $collector = new \Abava\Routing\Collector($this->routeParser, $this->dataGenerator);
        $collector->add($route);
        $url = $collector->url('route', ['param' => 'value']);
        $this->assertSame('/url/value', $url);
    }

    /**
     * @test
     */
    public function throwsExceptionIfRouteIsNotFound()
    {
        $this->expectException(InvalidArgumentException::class);

        $collector = new \Abava\Routing\Collector($this->routeParser, $this->dataGenerator);
        $collector->url('non-existing route');
    }

    /**
     * @test
     */
    public function tearDown()
    {
        Mockery::close();
    }

}
