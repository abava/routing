<?php declare(strict_types = 1);

class RouterTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testConstuctAndCollectRoutes()
    {
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Venta\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Venta\Container\Contract\CallerContract::class)->getMock();
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Venta\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Venta\Routing\MiddlewareCollector::class)->getMock();

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|PHPUnit_Framework_MockObject_MockObject|Invokable $collectionCallback */
        $collectionCallback = $this->getMockBuilder(Invokable::class)->getMock();
        $collectionCallback->expects($this->once())->method('invoke');

        $router = new \Venta\Routing\Router($caller, $collector, function($argument) use ($collectionCallback) {
            $collectionCallback->invoke($argument);
            $this->assertInstanceOf(\Venta\Routing\RoutesCollector::class, $argument);
        });
    }

    /**
     * @test
     */
    public function testCollectMiddlewares()
    {
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Venta\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Venta\Container\Contract\CallerContract::class)->getMock();
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Venta\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Venta\Routing\MiddlewareCollector::class)->getMock();

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|PHPUnit_Framework_MockObject_MockObject|Invokable $middlewareCallback */
        $middlewareCallback = $this->getMockBuilder(Invokable::class)->getMock();
        $middlewareCallback->expects($this->once())->method('invoke')->with($collector);

        $callback = function($argument)use($middlewareCallback){ $middlewareCallback->invoke($argument); };

        $router = new \Venta\Routing\Router($caller, $collector, function(){ return []; });
        $result = $router->collectMiddlewares($callback);
        $this->assertInstanceOf(\Venta\Routing\Contract\RouterContract::class, $result);
    }

    /**
     * @test
     */
    public function testDispatch()
    {
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\ResponseInterface $response */
        $response = $this->getMockBuilder(\Psr\Http\Message\ResponseInterface::class)->getMock();

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder(\Psr\Http\Message\RequestInterface::class)->getMock();
        $request->method('getMethod')->willReturn('GET');
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\UriInterface $uri */
        $uri = $this->getMockBuilder(\Psr\Http\Message\UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn('/url');
        $request->method('getUri')->willReturn($uri);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Venta\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Venta\Container\Contract\CallerContract::class)->getMock();
        $caller->method('call')->with('handle', [])->willReturn($response);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Venta\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Venta\Routing\MiddlewareCollector::class)->getMock();
        $collector->method('getMiddlewares')->willReturn([]);

        $router = new \Venta\Routing\Router($caller, $collector, function(\Venta\Routing\RoutesCollector $routesCollector){
            $routesCollector->get('/url', 'handle');
        });
        $result = $router->dispatch($request);
        $this->assertSame($response, $result);
    }

    /**
     * @test
     */
    public function testDispatchWithMiddleware()
    {
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\ResponseInterface $response */
        $response = $this->getMockBuilder(\Psr\Http\Message\ResponseInterface::class)->getMock();

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder(\Psr\Http\Message\RequestInterface::class)->getMock();
        $request->method('getMethod')->willReturn('GET');
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\UriInterface $uri */
        $uri = $this->getMockBuilder(\Psr\Http\Message\UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn('/url');
        $request->method('getUri')->willReturn($uri);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Venta\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Venta\Container\Contract\CallerContract::class)->getMock();
        $caller->method('call')->with('handle', [])->willReturn($response);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Venta\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Venta\Routing\MiddlewareCollector::class)->getMock();
        $middleware = new class implements \Venta\Routing\Contract\MiddlewareContract{
            public function handle(\Psr\Http\Message\RequestInterface $request, Closure $next) : \Psr\Http\Message\ResponseInterface { return $next($request); }
        };
        $collector->method('addMiddleware')->with('test', $middleware);
        $collector->method('getMiddlewares')->willReturn(['test'=>$middleware]);

        $router = new \Venta\Routing\Router($caller, $collector, function(\Venta\Routing\RoutesCollector $routesCollector){
            $routesCollector->get('/url', 'handle');
        });
        $router->collectMiddlewares(function($collector)use($middleware){ $collector->addMiddleware('test', $middleware); });
        $result = $router->dispatch($request);
        $this->assertSame($response, $result);
    }

    /**
     * @test
     */
    public function testDispatchWithStringControllerResult()
    {
        // todo Make consistent Response interface
        $this->markTestSkipped('Psr Response & Venta Response conflict about ->append() method');

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\ResponseInterface $response */
        $response = $this->getMockBuilder(\Psr\Http\Message\ResponseInterface::class)->getMock();
        $response->method('append')->with('string')->willReturnSelf();

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder(\Psr\Http\Message\RequestInterface::class)->getMock();
        $request->method('getMethod')->willReturn('GET');
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\UriInterface $uri */
        $uri = $this->getMockBuilder(\Psr\Http\Message\UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn('/url');
        $request->method('getUri')->willReturn($uri);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|PHPUnit_Framework_MockObject_MockObject|\Venta\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Venta\Container\Contract\CallerContract::class)->getMock();
        $caller->expects($this->exactly(2))->method('call')->withConsecutive(
            ['handle', []],
            ['\Venta\Framework\Http\Factory\ResponseFactory@new']
        )->willReturn(
            'string',
            $response
        );

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Venta\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Venta\Routing\MiddlewareCollector::class)->getMock();
        $collector->method('getMiddlewares')->willReturn([]);

        $router = new \Venta\Routing\Router($caller, $collector, function(\Venta\Routing\RoutesCollector $routesCollector){
            $routesCollector->get('/url', 'handle');
        });
        $result = $router->dispatch($request);
        $this->assertSame($response, $result);
    }

    /**
     * @test
     */
    public function testDispatchWithStringableControllerResult()
    {
        // todo Make consistent Response interface
        $this->markTestSkipped('Psr Response & Venta Response conflict about ->append() method');

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\ResponseInterface $response */
        $response = $this->getMockBuilder(\Psr\Http\Message\ResponseInterface::class)->getMock();

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|Stringable $stringable */
        $stringable = $this->getMockBuilder(Stringable::class)->getMock();
        $stringable->method('__toString')->with()->willReturn('string');

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder(\Psr\Http\Message\RequestInterface::class)->getMock();
        $request->method('getMethod')->willReturn('GET');
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\UriInterface $uri */
        $uri = $this->getMockBuilder(\Psr\Http\Message\UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn('/url');
        $request->method('getUri')->willReturn($uri);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Venta\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Venta\Container\Contract\CallerContract::class)->getMock();
        $caller->expects($this->exactly(2))->method('call')->withConsecutive(
            ['handle', []],
            ['\Venta\Framework\Http\Factory\ResponseFactory@new']
        )->willReturn(
            'string',
            $stringable
        );

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Venta\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Venta\Routing\MiddlewareCollector::class)->getMock();
        $collector->method('getMiddlewares')->willReturn([]);

        $router = new \Venta\Routing\Router($caller, $collector, function(\Venta\Routing\RoutesCollector $routesCollector){
            $routesCollector->get('/url', 'handle');
        });
        $result = $router->dispatch($request);
        $this->assertSame($response, $result);
    }

    /**
     * @test
     */
    public function testDispatchWithInvalidControllerResult()
    {
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder(\Psr\Http\Message\RequestInterface::class)->getMock();
        $request->method('getMethod')->willReturn('GET');
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\UriInterface $uri */
        $uri = $this->getMockBuilder(\Psr\Http\Message\UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn('/url');
        $request->method('getUri')->willReturn($uri);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Venta\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Venta\Container\Contract\CallerContract::class)->getMock();
        $caller->method('call')->with('handle', [])->willReturn(new stdClass());

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Venta\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Venta\Routing\MiddlewareCollector::class)->getMock();
        $collector->method('getMiddlewares')->willReturn([]);

        $router = new \Venta\Routing\Router($caller, $collector, function(\Venta\Routing\RoutesCollector $routesCollector){
            $routesCollector->get('/url', 'handle');
        });
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Controller action result must be either ResponseInterface or string');
        $router->dispatch($request);
    }

    /**
     * @test
     */
    public function testDispatchNotFound()
    {
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder(\Psr\Http\Message\RequestInterface::class)->getMock();
        $request->method('getMethod')->willReturn('GET');

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\UriInterface $uri */
        $uri = $this->getMockBuilder(\Psr\Http\Message\UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn('/url');
        $request->method('getUri')->willReturn($uri);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Venta\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Venta\Container\Contract\CallerContract::class)->getMock();
        $caller->method('call')->with('handle', [])->willReturn(new stdClass());

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Venta\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Venta\Routing\MiddlewareCollector::class)->getMock();
        $collector->method('getMiddlewares')->willReturn([]);

        $router = new \Venta\Routing\Router($caller, $collector, function(){});
        $this->expectException(\Venta\Routing\Exceptions\NotFoundException::class);
        $this->expectExceptionMessage('Can not route to this URI.');
        $router->dispatch($request);
    }

    /**
     * @test
     */
    public function testDispatchMethodNotAllowed()
    {
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder(\Psr\Http\Message\RequestInterface::class)->getMock();
        $request->method('getMethod')->willReturn('POST');

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\UriInterface $uri */
        $uri = $this->getMockBuilder(\Psr\Http\Message\UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn('/url');
        $request->method('getUri')->willReturn($uri);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Venta\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Venta\Container\Contract\CallerContract::class)->getMock();
        $caller->method('call')->with('handle', [])->willReturn(new stdClass());

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Venta\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Venta\Routing\MiddlewareCollector::class)->getMock();
        $collector->method('getMiddlewares')->willReturn([]);

        $router = new \Venta\Routing\Router($caller, $collector, function(\Venta\Routing\RoutesCollector $routesCollector){
            $routesCollector->get('/url', 'handle');
        });
        $this->expectException(\Venta\Routing\Exceptions\NotAllowedException::class);
        $this->expectExceptionMessage('Method is not allowed. Allowed methods are: GET, HEAD');
        $router->dispatch($request);
    }

}

interface Invokable {
    public function invoke($argument);
}

interface Stringable{
    public function __toString();
}