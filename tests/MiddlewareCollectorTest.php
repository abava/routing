<?php declare(strict_types = 1);

use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Venta\Routing\Contract\MiddlewareContract;

/**
 * Class MiddlewareCollectorTest
 */
class MiddlewareCollectorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \Venta\Routing\MiddlewareCollector
     */
    private $collector;

    public function setUp()
    {
        $this->collector = new \Venta\Routing\MiddlewareCollector();
    }

    /**
     * @test
     */
    public function canAddMiddlewareByContract()
    {
        $middleware = new class implements MiddlewareContract{
            public function handle(RequestInterface $request, Closure $next) : ResponseInterface { return $next($request); }
        };
        $this->collector->addContractMiddleware('test', $middleware);
        $middlewares = $this->collector->getMiddlewares();
        $this->assertCount(1, $middlewares);
        $this->assertArrayHasKey('test', $middlewares);
        $this->assertSame($middleware, $middlewares['test']);
    }

    /**
     * @test
     */
    public function canAddClosureMiddleware()
    {
        $middleware = function (RequestInterface $request, Closure $next): ResponseInterface {
            return $next($request->withHeader('header','request'))->withHeader('header','response');
        };

        $this->collector->addCallableMiddleware('test', $middleware);
        $middlewares = $this->collector->getMiddlewares();
        $this->assertCount(1, $middlewares);
        $this->assertArrayHasKey('test', $middlewares);
        $this->assertInstanceOf(MiddlewareContract::class, $middlewares['test']);

        // Checking if closure is still doing its job
        /** @var RequestInterface|PHPUnit_Framework_MockObject_Builder_InvocationMocker $request */
        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->method('withHeader')->with('header','request')->willReturnSelf();

        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response->method('withHeader')->with('header','response')->willReturnSelf();

        $next = function ($r) use ($request, $response) {
            $this->assertInstanceOf(RequestInterface::class, $r);
            $this->assertSame($request, $r);
            return $response;
        };

        $result = $middlewares['test']->handle($request, $next);
        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame($response, $result);
    }

    /**
     * @test
     */
    public function getMiddlewaresReversesArray()
    {
        $middleware1 = new class implements MiddlewareContract{
            public function handle(RequestInterface $request, Closure $next) : ResponseInterface { return $next($request); }
        };

        $middleware2 = new class implements MiddlewareContract{
            public function handle(RequestInterface $request, Closure $next) : ResponseInterface { return $next($request); }
        };

        $this->collector->addContractMiddleware('m1', $middleware1);
        $this->collector->addContractMiddleware('m2', $middleware2);
        $this->assertSame(['m2', 'm1'], array_keys($this->collector->getMiddlewares()));
    }

    /**
     * @test
     */
    public function cannotAddNonMiddlewareContactInstance()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Middleware must either implement MiddlewareContract or be callable');

        $this->collector->addMiddleware('test', 42);
    }

    /**
     * @test
     */
    public function canUseCommonAddMethod()
    {
        $contract = new class implements MiddlewareContract{
            public function handle(RequestInterface $request, Closure $next) : ResponseInterface { return $next($request); }
        };

        $closure = function (RequestInterface $request, Closure $next): ResponseInterface {
            return $next($request->withHeader('header','request'))->withHeader('header','response');
        };

        $this->collector->addMiddleware('contract', $contract);
        $this->collector->addMiddleware('closure', $closure);

        $collection = $this->collector->getMiddlewares();
        $this->assertCount(2, $collection);
        $this->assertArrayHasKey('contract', $collection);
        $this->assertArrayHasKey('closure', $collection);
        $this->assertInstanceOf(MiddlewareContract::class, $collection['closure']);
        $this->assertInstanceOf(MiddlewareContract::class, $collection['contract']);
    }

}