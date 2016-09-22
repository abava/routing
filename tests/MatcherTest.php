<?php

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface as Uri;

/**
 * Class MatcherTest
 */
class MatcherTest extends TestCase
{

    protected $collector;

    protected $dispatcher;

    protected $factory;

    protected $request;

    public function setUp()
    {
        $this->factory = Mockery::mock(\Venta\Contracts\Routing\DispatcherFactory::class);
        $this->request = Mockery::mock(\Psr\Http\Message\RequestInterface::class);
        $this->collector = Mockery::mock(\Venta\Contracts\Routing\RouteCollector::class);
        $this->dispatcher = Mockery::mock(\FastRoute\Dispatcher::class);
        $this->collector->shouldReceive('getFilteredData')->with($this->request)->andReturn(['data'])->once();
        $this->factory->shouldReceive('create')->with(['data'])->andReturn($this->dispatcher)->once();
        $this->request->shouldReceive('getMethod')->withNoArgs()->andReturn('GET')->once();
        $this->request->shouldReceive('getUri')->withNoArgs()
                      ->andReturn(
                          Mockery::mock(Uri::class)->shouldReceive('getPath')->andReturn('/url')->getMock()
                      )->once();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function testMatchFound()
    {
        // Match result for found route
        $match = [
            \FastRoute\Dispatcher::FOUND,
            $route = new \Venta\Routing\Route(['GET'], '/url', 'controller@action'),
            $params = ['param' => 'value'],
        ];

        // Mock dispatch result
        $this->dispatcher->shouldReceive('dispatch')->with('GET', '/url')->andReturn($match)->once();

        $matcher = new \Venta\Routing\RouteMatcher($this->factory);
        $result = $matcher->match($this->request, $this->collector);

        // Check for route params
        $this->assertInstanceOf(\Venta\Routing\Route::class, $result);
        $this->assertSame('/url', $result->getPath());
        $this->assertSame('controller@action', $result->getCallable());
        $this->assertSame($params, $result->getParameters());
        $this->assertNotSame($route, $result);
    }

    /**
     * @test
     */
    public function testMethodNotAllowed()
    {
        $this->expectException(\Venta\Routing\Exceptions\NotAllowedException::class);
        $this->expectExceptionMessageRegExp('/Method is not allowed/');
        $this->expectExceptionMessageRegExp('/POST/');
        $this->expectExceptionMessageRegExp('/PUT/');

        $match = [\FastRoute\Dispatcher::METHOD_NOT_ALLOWED, ['POST', 'PUT']];
        $this->dispatcher->shouldReceive('dispatch')->with('GET', '/url')->andReturn($match)->once();
        $matcher = new \Venta\Routing\RouteMatcher($this->factory);
        $matcher->match($this->request, $this->collector);
    }

    public function testNotFound()
    {
        $this->expectException(\Venta\Routing\Exceptions\NotFoundException::class);
        $this->expectExceptionMessage('Can not route to this URI.');

        $match = [\FastRoute\Dispatcher::NOT_FOUND];
        $this->dispatcher->shouldReceive('dispatch')->with('GET', '/url')->andReturn($match)->once();
        $matcher = new \Venta\Routing\RouteMatcher($this->factory);
        $matcher->match($this->request, $this->collector);
    }

}
