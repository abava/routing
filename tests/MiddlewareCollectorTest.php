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
        $request = $this->createMock(RequestInterface::class)
            ->method('withHeader')
            ->willReturn($this->returnArgument(1));

        $response = $this->createMock(ResponseInterface::class)
            ->method('withHeader')
            ->willReturn($this->returnArgument(1));

        $next = function ($string) use ($response) {
            $this->assertEquals('request', $string);
            return $response;
        };

        $result = $middlewares['test']->handle($request, $next);
        $this->assertEquals('response', $result);
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

}