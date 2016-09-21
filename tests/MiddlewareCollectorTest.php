<?php

use PHPUnit\Framework\TestCase;

class MiddlewareCollectorTest extends TestCase
{

    /**
     * @var \Venta\Routing\Middleware\Collector|\Mockery\MockInterface
     */
    protected $collector;

    /**
     * @var \Venta\Container\Contract\Container|\Mockery\MockInterface
     */
    protected $container;

    public function setUp()
    {
        $this->container = Mockery::mock(\Venta\Container\Contract\Container::class);
        $this->collector = new \Venta\Routing\Middleware\Collector($this->container);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function canIterateMiddlewares()
    {
        $request = Mockery::mock(\Psr\Http\Message\RequestInterface::class);
        $first = Mockery::mock(\Venta\Routing\Contract\Middleware::class);
        $first->shouldReceive('handle')
              ->with($request, Mockery::type(Closure::class))
              ->andReturnUsing(function ($request, $next) {
                  echo '1';
                  $response = $next($request);
                  echo '1';

                  return $response;
              })
              ->once();
        $second = Mockery::mock(\Venta\Routing\Contract\Middleware::class);
        $second->shouldReceive('handle')
               ->with($request, Mockery::type(Closure::class))
               ->andReturnUsing(function ($request, $next) {
                   echo '2';
                   $response = $next($request);
                   echo '2';

                   return $response;
               })
               ->once();
        $third = Mockery::mock(\Venta\Routing\Contract\Middleware::class);
        $third->shouldReceive('handle')
              ->with($request, Mockery::type(Closure::class))
              ->andReturnUsing(function ($request, $next) {
                  echo '3';
                  $response = $next($request);
                  echo '3';

                  return $response;
              })
              ->once();
        $last = function () {
            echo 'last';

            return Mockery::mock(\Psr\Http\Message\ResponseInterface::class);
        };
        $this->collector->pushMiddleware('first', $first);
        $this->collector->pushMiddleware('second', $second);
        $this->collector->pushMiddleware('third', $third);

        $next = $last;
        $names = [];
        foreach ($this->collector as $name => $middleware) {
            $names[] = $name;
            $next = function ($request) use ($middleware, $next) {
                /** @var \Venta\Routing\Contract\Middleware $middleware */
                return $middleware->handle($request, $next);
            };
        }

        $this->expectOutputString('123last321');
        $response = $next($request);
        $this->assertSame(['third', 'second', 'first'], $names);
        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $response);
    }

    /**
     * @test
     */
    public function canPushAfter()
    {
        /** @var \Venta\Routing\Middleware\Collector $collector */
        $collector = new class($this->container) extends \Venta\Routing\Middleware\Collector
        {
            public function getOrder()
            {
                return $this->order;
            }
        };
        $collector->pushMiddleware('first', function () {
        });
        $collector->pushMiddleware('third', function () {
        });
        $collector->pushAfter('first', 'second', function () {
        });
        $this->assertSame(['first', 'second', 'third'], $collector->getOrder());
    }

    /**
     * @test
     */
    public function canPushAfterInReversedMode()
    {
        $this->collector->pushMiddleware('middleware', function () {
        });
        $this->collector->rewind();

        $this->expectException(RuntimeException::class);

        $this->collector->pushAfter('middleware', 'another', function () {
        });
    }

    /**
     * @test
     */
    public function canPushBefore()
    {
        /** @var \Venta\Routing\Middleware\Collector $collector */
        $collector = new class($this->container) extends \Venta\Routing\Middleware\Collector
        {
            public function getOrder()
            {
                return $this->order;
            }
        };
        $collector->pushMiddleware('first', function () {
        });
        $collector->pushMiddleware('third', function () {
        });
        $collector->pushBefore('third', 'second', function () {
        });
        $this->assertSame(['first', 'second', 'third'], $collector->getOrder());
    }

    /**
     * @test
     */
    public function canPushBeforeInReversedMode()
    {
        $this->collector->pushMiddleware('middleware', function () {
        });
        $this->collector->rewind();

        $this->expectException(RuntimeException::class);

        $this->collector->pushBefore('middleware', 'another', function () {
        });
    }

    /**
     * @test
     */
    public function canPushMiddlewareAsClosure()
    {
        $this->collector->pushMiddleware('middleware', function ($request, $next) {
            return $next($request);
        });
        $middleware = $this->collector->current();
        $this->assertInstanceOf(\Venta\Routing\Contract\Middleware::class, $middleware);
        $response = Mockery::mock(\Psr\Http\Message\ResponseInterface::class);
        $result = $middleware->handle(
            Mockery::mock(\Psr\Http\Message\RequestInterface::class),
            function (\Psr\Http\Message\RequestInterface $request) use ($response) {
                return $response;
            }
        );
        $this->assertSame($response, $result);
    }

    /**
     * @test
     */
    public function canPushMiddlewareAsString()
    {
        $middleware = Mockery::mock(\Venta\Routing\Contract\Middleware::class);
        $this->container->shouldReceive('make')
                        ->with(get_class($middleware))
                        ->andReturn($middleware)
                        ->once();
        $this->collector->pushMiddleware('middleware', get_class($middleware));
        $this->assertSame($middleware, $this->collector->current());
        // Check that container->make() is called only once,
        // saving the result in $middlewares property
        $this->assertSame($middleware, $this->collector->current());
    }

    /**
     * @test
     */
    public function canReverseOrder()
    {
        $middleware = Mockery::mock(\Venta\Routing\Contract\Middleware::class);
        $this->collector->pushMiddleware('first', $middleware);
        $this->collector->rewind();
        $this->collector->pushMiddleware('second', $middleware);
        $middlewares = array_keys(iterator_to_array($this->collector, true));
        // Middlewares must be returned (traversed) in reversed order
        $this->assertSame(['second', 'first'], $middlewares);
    }

    /**
     * @test
     */
    public function testPushMiddleware()
    {
        $this->assertFalse($this->collector->has('middleware'));
        $this->collector->pushMiddleware('middleware', Mockery::mock(\Venta\Routing\Contract\Middleware::class));
        $this->assertTrue($this->collector->has('middleware'));
    }

    /**
     * @test
     */
    public function throwsExceptionOnMiddlewareWithTheSameNameTwice()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->collector->pushMiddleware('middleware', Mockery::mock(\Venta\Routing\Contract\Middleware::class));
        $this->collector->pushMiddleware('middleware', Mockery::mock(\Venta\Routing\Contract\Middleware::class));
    }

    /**
     * @test
     */
    public function throwsExceptionOnPushAfterExistingMiddlewareTwice()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->collector->pushMiddleware('middleware', function () {
        });
        $this->collector->pushAfter('middleware', 'middleware', function () {
        });
    }

    /**
     * @test
     */
    public function throwsExceptionOnPushAfterInvalidMiddleware()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->collector->pushMiddleware('middleware', function () {
        });
        $this->collector->pushAfter('middleware', 'name', new stdClass());
    }

    /**
     * @test
     */
    public function throwsExceptionOnPushAfterNonExistingMiddleware()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->collector->pushAfter('non-existing', 'middleware', function () {
        });
    }

    /**
     * @test
     */
    public function throwsExceptionOnPushBeforeExistingMiddlewareTwice()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->collector->pushMiddleware('middleware', function () {
        });
        $this->collector->pushBefore('middleware', 'middleware', function () {
        });
    }

    /**
     * @test
     */
    public function throwsExceptionOnPushBeforeInvalidMiddleware()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->collector->pushMiddleware('middleware', function () {
        });
        $this->collector->pushBefore('middleware', 'name', new stdClass());
    }

    /**
     * @test
     */
    public function throwsExceptionOnPushBeforeNonExistingMiddleware()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->collector->pushBefore('non-existing', 'middleware', function () {
        });
    }

    /**
     * @test
     */
    public function throwsExceptionOnPushInvalidMiddleware()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->collector->pushMiddleware('middleware', new stdClass());
    }

}
