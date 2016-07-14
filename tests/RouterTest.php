<?php declare(strict_types = 1);

class RouterTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testConstuctAndCollectRoutes()
    {
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Abava\Container\Contract\CallerContract::class)->getMock();
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Abava\Routing\MiddlewareCollector::class)->getMock();

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|PHPUnit_Framework_MockObject_MockObject|Invokable $collectionCallback */
        $collectionCallback = $this->getMockBuilder(Invokable::class)->getMock();
        $collectionCallback->expects($this->once())->method('invoke');

        $router = new \Abava\Routing\Router($caller, $collector, function($argument) use ($collectionCallback) {
            $collectionCallback->invoke($argument);
            $this->assertInstanceOf(\Abava\Routing\RoutesCollector::class, $argument);
        });
    }

    /**
     * @test
     */
    public function testCollectMiddlewares()
    {
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Abava\Container\Contract\CallerContract::class)->getMock();
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Abava\Routing\MiddlewareCollector::class)->getMock();

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|PHPUnit_Framework_MockObject_MockObject|Invokable $middlewareCallback */
        $middlewareCallback = $this->getMockBuilder(Invokable::class)->getMock();
        $middlewareCallback->expects($this->once())->method('invoke')->with($collector);

        $callback = function($argument)use($middlewareCallback){ $middlewareCallback->invoke($argument); };

        $router = new \Abava\Routing\Router($caller, $collector, function(){ return []; });
        $result = $router->collectMiddlewares($callback);
        $this->assertInstanceOf(\Abava\Routing\Contract\RouterContract::class, $result);
    }

    /**
     * @test
     */
    public function testDispatch()
    {
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Http\Contract\ResponseContract $response */
        $response = $this->getMockBuilder(\Abava\Http\Contract\ResponseContract::class)->getMock();

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Http\Contract\RequestContract $request */
        $request = $this->getMockBuilder(\Abava\Http\Contract\RequestContract::class)->getMock();
        $request->method('getMethod')->willReturn('GET');
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\UriInterface $uri */
        $uri = $this->getMockBuilder(\Psr\Http\Message\UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn('/url/value');
        $request->method('getUri')->willReturn($uri);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Abava\Container\Contract\CallerContract::class)->getMock();
        $caller->method('call')->with('handle', ['param'=>'value'])->willReturn($response);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Abava\Routing\MiddlewareCollector::class)->getMock();
        $collector->method('getMiddlewares')->willReturn([]);

        $router = new \Abava\Routing\Router($caller, $collector, function(\Abava\Routing\RoutesCollector $routesCollector){
            $routesCollector->get('/url/{param}', 'handle');
        });
        $result = $router->dispatch($request);
        $this->assertSame($response, $result);
    }

    /**
     * @test
     */
    public function testDispatchWithMiddleware()
    {
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Http\Contract\ResponseContract $response */
        $response = $this->getMockBuilder(\Abava\Http\Contract\ResponseContract::class)->getMock();

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Http\Contract\RequestContract $request */
        $request = $this->getMockBuilder(\Abava\Http\Contract\RequestContract::class)->getMock();
        $request->method('getMethod')->willReturn('GET');
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\UriInterface $uri */
        $uri = $this->getMockBuilder(\Psr\Http\Message\UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn('/url');
        $request->method('getUri')->willReturn($uri);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Abava\Container\Contract\CallerContract::class)->getMock();
        $caller->method('call')->with('handle', [])->willReturn($response);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Abava\Routing\MiddlewareCollector::class)->getMock();
        $middleware = new class implements \Abava\Routing\Contract\MiddlewareContract{
            public function handle(\Abava\Http\Contract\RequestContract $request, Closure $next) : \Abava\Http\Contract\ResponseContract { return $next($request); }
        };
        $collector->method('addMiddleware')->with('test', $middleware);
        $collector->method('getMiddlewares')->willReturn(['test'=>$middleware]);

        $router = new \Abava\Routing\Router($caller, $collector, function(\Abava\Routing\RoutesCollector $routesCollector){
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
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Http\Contract\ResponseContract $response */
        $response = $this->getMockBuilder(\Abava\Http\Contract\ResponseContract::class)->getMock();
        $response->method('append')->with('string')->willReturnSelf();

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Http\Contract\RequestContract $request */
        $request = $this->getMockBuilder(\Abava\Http\Contract\RequestContract::class)->getMock();
        $request->method('getMethod')->willReturn('GET');
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\UriInterface $uri */
        $uri = $this->getMockBuilder(\Psr\Http\Message\UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn('/url');
        $request->method('getUri')->willReturn($uri);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|PHPUnit_Framework_MockObject_MockObject|\Abava\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Abava\Container\Contract\CallerContract::class)->getMock();
        $caller->expects($this->exactly(2))->method('call')->withConsecutive(
            ['handle', []],
            ['\Abava\Http\Factory\ResponseFactory@new']
        )->willReturn(
            'string',
            $response
        );

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Abava\Routing\MiddlewareCollector::class)->getMock();
        $collector->method('getMiddlewares')->willReturn([]);

        $router = new \Abava\Routing\Router($caller, $collector, function(\Abava\Routing\RoutesCollector $routesCollector){
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
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Http\Contract\ResponseContract $response */
        $response = $this->getMockBuilder(\Abava\Http\Contract\ResponseContract::class)->getMock();
        $response->method('append')->with('string')->willReturnSelf();

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|Stringable $stringable */
        $stringable = $this->getMockBuilder(Stringable::class)->getMock();
        $stringable->method('__toString')->with()->willReturn('string');

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Http\Contract\RequestContract $request */
        $request = $this->getMockBuilder(\Abava\Http\Contract\RequestContract::class)->getMock();
        $request->method('getMethod')->willReturn('GET');
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\UriInterface $uri */
        $uri = $this->getMockBuilder(\Psr\Http\Message\UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn('/url');
        $request->method('getUri')->willReturn($uri);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Abava\Container\Contract\CallerContract::class)->getMock();
        $caller->expects($this->exactly(2))->method('call')->withConsecutive(
            ['handle', []],
            ['\Abava\Http\Factory\ResponseFactory@new']
        )->willReturn(
            $stringable,
            $response
        );

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Abava\Routing\MiddlewareCollector::class)->getMock();
        $collector->method('getMiddlewares')->willReturn([]);

        $router = new \Abava\Routing\Router($caller, $collector, function(\Abava\Routing\RoutesCollector $routesCollector){
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
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Http\Contract\RequestContract $request */
        $request = $this->getMockBuilder(\Abava\Http\Contract\RequestContract::class)->getMock();
        $request->method('getMethod')->willReturn('GET');
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\UriInterface $uri */
        $uri = $this->getMockBuilder(\Psr\Http\Message\UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn('/url');
        $request->method('getUri')->willReturn($uri);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Abava\Container\Contract\CallerContract::class)->getMock();
        $caller->method('call')->with('handle', [])->willReturn(new stdClass());

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Abava\Routing\MiddlewareCollector::class)->getMock();
        $collector->method('getMiddlewares')->willReturn([]);

        $router = new \Abava\Routing\Router($caller, $collector, function(\Abava\Routing\RoutesCollector $routesCollector){
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
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Http\Contract\RequestContract $request */
        $request = $this->getMockBuilder(\Abava\Http\Contract\RequestContract::class)->getMock();
        $request->method('getMethod')->willReturn('GET');

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\UriInterface $uri */
        $uri = $this->getMockBuilder(\Psr\Http\Message\UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn('/url');
        $request->method('getUri')->willReturn($uri);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Abava\Container\Contract\CallerContract::class)->getMock();
        $caller->method('call')->with('handle', [])->willReturn(new stdClass());

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Abava\Routing\MiddlewareCollector::class)->getMock();
        $collector->method('getMiddlewares')->willReturn([]);

        $router = new \Abava\Routing\Router($caller, $collector, function(){});
        $this->expectException(\Abava\Routing\Exceptions\NotFoundException::class);
        $this->expectExceptionMessage('Can not route to this URI.');
        $router->dispatch($request);
    }

    /**
     * @test
     */
    public function testDispatchNotFoundDueToParameterMismatch()
    {
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Http\Contract\RequestContract $request */
        $request = $this->getMockBuilder(\Abava\Http\Contract\RequestContract::class)->getMock();
        $request->method('getMethod')->willReturn('GET');

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\UriInterface $uri */
        $uri = $this->getMockBuilder(\Psr\Http\Message\UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn('/url/word');
        $request->method('getUri')->willReturn($uri);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Abava\Container\Contract\CallerContract::class)->getMock();
        $caller->method('call')->with('handle', [])->willReturn(new stdClass());

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Abava\Routing\MiddlewareCollector::class)->getMock();
        $collector->method('getMiddlewares')->willReturn([]);

        $router = new \Abava\Routing\Router($caller, $collector, function(\Abava\Routing\RoutesCollector $collector){
            $collector->get('/url/{number:\d+}', 'handle');
        });
        $this->expectException(\Abava\Routing\Exceptions\NotFoundException::class);
        $this->expectExceptionMessage('Can not route to this URI.');
        $router->dispatch($request);
    }

    /**
     * @test
     */
    public function testDispatchMethodNotAllowed()
    {
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Http\Contract\RequestContract $request */
        $request = $this->getMockBuilder(\Abava\Http\Contract\RequestContract::class)->getMock();
        $request->method('getMethod')->willReturn('POST');

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Psr\Http\Message\UriInterface $uri */
        $uri = $this->getMockBuilder(\Psr\Http\Message\UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn('/url');
        $request->method('getUri')->willReturn($uri);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Container\Contract\CallerContract $caller */
        $caller = $this->getMockBuilder(\Abava\Container\Contract\CallerContract::class)->getMock();
        $caller->method('call')->with('handle', [])->willReturn(new stdClass());

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\Abava\Routing\MiddlewareCollector $collector */
        $collector = $this->getMockBuilder(\Abava\Routing\MiddlewareCollector::class)->getMock();
        $collector->method('getMiddlewares')->willReturn([]);

        $router = new \Abava\Routing\Router($caller, $collector, function(\Abava\Routing\RoutesCollector $routesCollector){
            $routesCollector->get('/url', 'handle');
        });
        $this->expectException(\Abava\Routing\Exceptions\NotAllowedException::class);
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