<?php

use PHPUnit\Framework\TestCase;

/**
 * Class GroupTest
 */
class GroupTest extends TestCase
{

    /**
     * @var \Venta\Routing\RouteCollector|\Mockery\MockInterface
     */
    protected $collector;

    public function setUp()
    {
        $this->collector = Mockery::mock(\Venta\Routing\RouteCollector::class);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function callbackIsCalledOnCollect()
    {
        $callbackMock = Mockery::mock(function () {
        });
        $group = new \Venta\Routing\RouteGroup('/', [$callbackMock, '__invoke'], $this->collector);
        $callbackMock->shouldReceive('__invoke')
                     ->with($group)
                     ->once();
        $group->collect();
    }

    /**
     * @test
     */
    public function canAddRoute()
    {
        $group = new \Venta\Routing\RouteGroup('/', function () {
        }, $this->collector);
        $group->addRoute('GET', '/', 'handle');
        $existingRoutes = $group->getRoutes();
        $this->assertCount(1, $existingRoutes);
        $this->assertInstanceOf(\Venta\Routing\Route::class, $existingRoutes[0]);
    }

    /**
     * @test
     */
    public function canAddRouteInstance()
    {
        $route = new \Venta\Routing\Route(['GET'], '/', 'callable');
        $group = new \Venta\Routing\RouteGroup('/', function () {
        }, $this->collector);
        $group->add($route);
        $this->assertContains($route, $group->getRoutes());
    }

    /**
     * @test
     */
    public function canProxyToCollectorInstance()
    {
        $callback = function () {
        };
        $group = new \Venta\Routing\RouteGroup('/', $callback, $this->collector);
        $this->collector->shouldReceive('getData')->withNoArgs()->andReturn(['data'])->once();
        $this->assertSame(['data'], $group->getData());
        $request = Mockery::mock(\Psr\Http\Message\RequestInterface::class);
        $this->collector->shouldReceive('getFilteredData')->with($request)->andReturn(['data'])->once();
        $this->assertSame(['data'], $group->getFilteredData($request));
        $group->setHost('localhost');
        $group->setScheme('https');
        $groupMock = Mockery::mock(\Venta\Contracts\Routing\RouteGroup::class);
        $groupMock->shouldReceive('setHost')->with('localhost')->andReturnSelf()->once();
        $groupMock->shouldReceive('setScheme')->with('https')->andReturnSelf()->once();
        $this->collector->shouldReceive('group')
                        ->with('/prefix', $callback)
                        ->andReturn($groupMock)
                        ->once();
        $this->assertSame($groupMock, $group->group('prefix', $callback));
    }

    /**
     * @test
     */
    public function canSetPrefix()
    {
        $group = new \Venta\Routing\RouteGroup('/', function () {
        }, $this->collector);
        $this->collector->shouldReceive('addRoute')->with('GET', '/abcdef', 'handler');
        $group->addRoute('GET', '/abcdef', 'handler');
        $group->setPrefix('/qwerty');
        $this->collector->shouldReceive('addRoute')->with('POST', '/qwerty/zxcv', 'handle');
        $group->addRoute('POST', '/zxcv', 'handle');
    }

    /**
     * @test
     */
    public function collectSetsHostAndScheme()
    {
        $callback = function (\Venta\Contracts\Routing\RouteCollector $collector) {
            $collector->add(new \Venta\Routing\Route(['GET'], 'abc', 'handle'));
            $collector->add(
                (new \Venta\Routing\Route(['POST'], 'def', 'handle'))
                    ->withHost('127.0.0.1')
                    ->withScheme('http')
            );
        };
        $group = new \Venta\Routing\RouteGroup('/prefix', $callback, $this->collector);
        $group->setHost('localhost');
        $group->setScheme('https');
        $this->collector->shouldReceive('add')->with(Mockery::on(function (\Venta\Routing\Route $route) {
            $this->assertEquals('https', $route->getScheme());
            $this->assertEquals('localhost', $route->getHost());
            $this->assertContains('prefix', $route->getPath());

            return true;
        }))->twice();
        $group->collect();
    }

}
