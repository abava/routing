<?php

use Psr\Http\Message\UriInterface as Uri;

class MatcherTest extends PHPUnit_Framework_TestCase
{

    protected $factory;
    protected $request;
    protected $collector;
    protected $dispatcher;

    public function setUp()
    {
        $this->factory = Mockery::mock(\Abava\Routing\Contract\Dispatcher\Factory::class);
        $this->request = Mockery::mock(\Psr\Http\Message\RequestInterface::class);
        $this->collector = Mockery::mock(\Abava\Routing\Contract\Collector::class);
        $this->dispatcher = Mockery::mock(\FastRoute\Dispatcher::class);
        $this->collector->shouldReceive('getFilteredData')->with($this->request)->andReturn(['data'])->once();
        $this->factory->shouldReceive('make')->with(['data'])->andReturn($this->dispatcher)->once();
        $this->request->shouldReceive('getMethod')->withNoArgs()->andReturn('GET')->once();
        $this->request->shouldReceive('getUri')->withNoArgs()
            ->andReturn(
                Mockery::mock(Uri::class)->shouldReceive('getPath')->andReturn('/url')->getMock()
            )->once();
    }

    public function testMatchFound()
    {
        // Match result for found route
        $match = [
            \FastRoute\Dispatcher::FOUND,
            $route = new \Abava\Routing\Route(['GET'], '/url', 'controller@action'),
            $params = ['param' => 'value']
        ];

        // Mock dispatch result
        $this->dispatcher->shouldReceive('dispatch')->with('GET', '/url')->andReturn($match)->once();

        $matcher = new \Abava\Routing\Matcher($this->factory);
        $result = $matcher->match($this->request, $this->collector);

        // Check for route params
        $this->assertInstanceOf(\Abava\Routing\Route::class, $result);
        $this->assertSame('/url', $result->getPath());
        $this->assertSame('controller@action', $result->getCallable());
        $this->assertSame($params, $result->getParameters());
        $this->assertNotSame($route, $result);
    }

    public function testMethodNotAllowed()
    {
        $this->expectException(\Abava\Routing\Exceptions\NotAllowedException::class);
        $this->expectExceptionMessageRegExp('/Method is not allowed/');
        $this->expectExceptionMessageRegExp('/POST/');
        $this->expectExceptionMessageRegExp('/PUT/');

        $match = [\FastRoute\Dispatcher::METHOD_NOT_ALLOWED, ['POST', 'PUT']];
        $this->dispatcher->shouldReceive('dispatch')->with('GET', '/url')->andReturn($match)->once();
        $matcher = new \Abava\Routing\Matcher($this->factory);
        $matcher->match($this->request, $this->collector);
    }

    public function testNotFound()
    {
        $this->expectException(\Abava\Routing\Exceptions\NotFoundException::class);
        $this->expectExceptionMessage('Can not route to this URI.');

        $match = [\FastRoute\Dispatcher::NOT_FOUND];
        $this->dispatcher->shouldReceive('dispatch')->with('GET', '/url')->andReturn($match)->once();
        $matcher = new \Abava\Routing\Matcher($this->factory);
        $matcher->match($this->request, $this->collector);
    }

    public function tearDown()
    {
        Mockery::close();
    }

}
