<?php

/**
 * Class GroupTest
 */
class GroupTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \Abava\Routing\Collector|\Mockery\MockInterface
     */
    protected $collector;

    public function setUp()
    {
        $this->collector = Mockery::mock(\Abava\Routing\Collector::class);
    }

    public function testAddRouteInstance()
    {
        $route = new \Abava\Routing\Route(['GET'], '/', 'callable');
        $group = new \Abava\Routing\Group('/', function () {}, $this->collector);
        $group->add($route);
        $this->assertContains($route, $group->getRoutes());
    }

    public function testAddRoute()
    {
        $this->collector->shouldReceive('addRoute')->with('GET', '/', 'handle')->once();
        $group = new \Abava\Routing\Group('/', function () { }, $this->collector);
        $group->addRoute('GET', '/', 'handle');
    }

    public function testSetPrefix()
    {
        $group = new \Abava\Routing\Group('/', function () { }, $this->collector);
        $this->collector->shouldReceive('addRoute')->with('GET', '/abcdef', 'handler');
        $group->addRoute('GET', '/abcdef', 'handler');
        $group->setPrefix('/qwerty');
        $this->collector->shouldReceive('addRoute')->with('POST', '/qwerty/zxcv', 'handle');
        $group->addRoute('POST', '/zxcv', 'handle');
    }

    public function testCallbackIsCalledOnCollect()
    {
        $callbackMock = Mockery::mock(function(){});
        $group = new \Abava\Routing\Group('/', [$callbackMock, '__invoke'], $this->collector);
        $callbackMock->shouldReceive('__invoke')
            ->with($group)
            ->once();
        $group->collect();
    }

    public function testCollectSetsHostAndScheme()
    {
        $callback = function (\Abava\Routing\Contract\Collector $collector) {
            $collector->add(new \Abava\Routing\Route(['GET'], 'abc', 'handle'));
            $collector->add(
                (new \Abava\Routing\Route(['POST'], 'def', 'handle'))
                ->withHost('127.0.0.1')
                ->withScheme('http')
            );
        };
        $group = new \Abava\Routing\Group('/prefix', $callback, $this->collector);
        $group->setHost('localhost');
        $group->setScheme('https');
        $this->collector->shouldReceive('add')->with(Mockery::on(function (\Abava\Routing\Route $route) {
            $this->assertEquals('https', $route->getScheme());
            $this->assertEquals('localhost', $route->getHost());
            $this->assertContains('prefix', $route->getPath());
            return true;
        }))->twice();
        $group->collect();
    }

    public function testProxyToCollectorInstance()
    {
        $callback = function(){};
        $group = new \Abava\Routing\Group('/', $callback, $this->collector);
        $this->collector->shouldReceive('getData')->withNoArgs()->andReturn(['data'])->once();
        $this->assertSame(['data'], $group->getData());
        $request = Mockery::mock(\Psr\Http\Message\RequestInterface::class);
        $this->collector->shouldReceive('getFilteredData')->with($request)->andReturn(['data'])->once();
        $this->assertSame(['data'], $group->getFilteredData($request));
        $groupMock = Mockery::mock(\Abava\Routing\Contract\Group::class);
        $this->collector->shouldReceive('group')
            ->with('prefix', $callback)
            ->andReturn($groupMock)
            ->once();
        $this->assertSame($groupMock, $group->group('prefix', $callback));
    }

    public function tearDown()
    {
        Mockery::close();
    }

}
