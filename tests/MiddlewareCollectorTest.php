<?php declare(strict_types = 1);

use Abava\Http\Contract\{
    Request, Response
};
use Abava\Routing\Contract\Middleware;

/**
 * Class MiddlewareCollectorTest
 */
class MiddlewareCollectorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \Abava\Routing\MiddlewareCollector
     */
    private $collector;

    public function setUp()
    {
        $this->collector = new \Abava\Routing\MiddlewareCollector();
    }

    /**
     * @test
     */
    public function canAddMiddlewareByContract()
    {
        $middleware = new class implements Middleware
        {
            public function handle(Request $request, Closure $next) : Response
            {
                return $next($request);
            }
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
        $middleware = function (Request $request, Closure $next): Response {
            return $next($request->withHeader('header', 'request'))->withHeader('header', 'response');
        };

        $this->collector->addCallableMiddleware('test', $middleware);
        $middlewares = $this->collector->getMiddlewares();
        $this->assertCount(1, $middlewares);
        $this->assertArrayHasKey('test', $middlewares);
        $this->assertInstanceOf(Middleware::class, $middlewares['test']);

        // Checking if closure is still doing its job
        /** @var Request|PHPUnit_Framework_MockObject_Builder_InvocationMocker $request */
        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->method('withHeader')->with('header', 'request')->willReturnSelf();

        $response = $this->getMockBuilder(Response::class)->getMock();
        $response->method('withHeader')->with('header', 'response')->willReturnSelf();

        $next = function ($r) use ($request, $response) {
            $this->assertInstanceOf(Request::class, $r);
            $this->assertSame($request, $r);
            return $response;
        };

        $result = $middlewares['test']->handle($request, $next);
        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame($response, $result);
    }

    /**
     * @test
     */
    public function getMiddlewaresReversesArray()
    {
        $middleware1 = new class implements Middleware
        {
            public function handle(Request $request, Closure $next) : Response
            {
                return $next($request);
            }
        };

        $middleware2 = new class implements Middleware
        {
            public function handle(Request $request, Closure $next) : Response
            {
                return $next($request);
            }
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
        $this->expectExceptionMessage('Middleware must either implement Middleware contract or be callable');

        $this->collector->addMiddleware('test', 42);
    }

    /**
     * @test
     */
    public function canUseCommonAddMethod()
    {
        $contract = new class implements Middleware
        {
            public function handle(Request $request, Closure $next) : Response
            {
                return $next($request);
            }
        };

        $closure = function (Request $request, Closure $next): Response {
            return $next($request->withHeader('header', 'request'))->withHeader('header', 'response');
        };

        $this->collector->addMiddleware('contract', $contract);
        $this->collector->addMiddleware('closure', $closure);

        $collection = $this->collector->getMiddlewares();
        $this->assertCount(2, $collection);
        $this->assertArrayHasKey('contract', $collection);
        $this->assertArrayHasKey('closure', $collection);
        $this->assertInstanceOf(Middleware::class, $collection['closure']);
        $this->assertInstanceOf(Middleware::class, $collection['contract']);
    }

}