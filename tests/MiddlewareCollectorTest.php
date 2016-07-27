<?php

class MiddlewareCollectorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \Abava\Container\Contract\Container|\Mockery\MockInterface
     */
    protected $container;

    /**
     * @var \Abava\Routing\Middleware\Collector|\Mockery\MockInterface
     */
    protected $collector;

    public function setUp()
    {
        $this->container = Mockery::mock(\Abava\Container\Contract\Container::class);
        $this->collector = new \Abava\Routing\Middleware\Collector($this->container);
    }

    public function testPushMiddleware()
    {
        $this->assertFalse($this->collector->has('middleware'));
        $this->collector->pushMiddleware('middleware', Mockery::mock(\Abava\Routing\Contract\Middleware::class));
        $this->assertTrue($this->collector->has('middleware'));
    }

    public function testPushMiddlewareWithTheSameNameTwice()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->collector->pushMiddleware('middleware', Mockery::mock(\Abava\Routing\Contract\Middleware::class));
        $this->collector->pushMiddleware('middleware', Mockery::mock(\Abava\Routing\Contract\Middleware::class));
    }

    public function testPushInvalidMiddleware()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->collector->pushMiddleware('middleware', new stdClass());
    }

    public function testPushBefore()
    {
        /** @var \Abava\Routing\Middleware\Collector $collector */
        $collector = new class($this->container) extends \Abava\Routing\Middleware\Collector{
            public function getOrder() { return $this->order; }
        };
        $collector->pushMiddleware('first', function(){});
        $collector->pushMiddleware('third', function(){});
        $collector->pushBefore('third', 'second', function(){});
        $this->assertSame(['first', 'second', 'third'], $collector->getOrder());
    }

    public function testPushAfter()
    {
        /** @var \Abava\Routing\Middleware\Collector $collector */
        $collector = new class($this->container) extends \Abava\Routing\Middleware\Collector{
            public function getOrder() { return $this->order; }
        };
        $collector->pushMiddleware('first', function(){});
        $collector->pushMiddleware('third', function(){});
        $collector->pushAfter('first', 'second', function(){});
        $this->assertSame(['first', 'second', 'third'], $collector->getOrder());
    }

    public function testPushBeforeNonExistingMiddleware()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->collector->pushBefore('non-existing', 'middleware', function (){});
    }

    public function testPushAfterNonExistingMiddleware()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->collector->pushAfter('non-existing', 'middleware', function (){});
    }

    public function testPushBeforeExistingMiddlewareTwice()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->collector->pushMiddleware('middleware', function (){});
        $this->collector->pushBefore('middleware', 'middleware', function (){});
    }

    public function testPushAfterExistingMiddlewareTwice()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->collector->pushMiddleware('middleware', function (){});
        $this->collector->pushAfter('middleware', 'middleware', function (){});
    }

    public function testPushBeforeInvalidMiddleware()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->collector->pushMiddleware('middleware', function (){});
        $this->collector->pushBefore('middleware', 'name', new stdClass());
    }

    public function testPushAfterInvalidMiddleware()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->collector->pushMiddleware('middleware', function (){});
        $this->collector->pushAfter('middleware', 'name', new stdClass());
    }

    public function testCurrentMiddlewareIsString()
    {
        $middleware = Mockery::mock(\Abava\Routing\Contract\Middleware::class);
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

    public function testCurrentMiddlewareIsClosure()
    {
        $this->collector->pushMiddleware('middleware', function($request, $next){ return $next($request); });
        $middleware = $this->collector->current();
        $this->assertInstanceOf(\Abava\Routing\Contract\Middleware::class, $middleware);
        $response = Mockery::mock(\Psr\Http\Message\ResponseInterface::class);
        $result = $middleware->handle(
            Mockery::mock(\Psr\Http\Message\RequestInterface::class),
            function(\Psr\Http\Message\RequestInterface $request) use ($response) { return $response; }
        );
        $this->assertSame($response, $result);
    }

    public function testCollectorIterator()
    {
        $request = Mockery::mock(\Psr\Http\Message\RequestInterface::class);
        $first = Mockery::mock(\Abava\Routing\Contract\Middleware::class);
        $first->shouldReceive('handle')
            ->with($request, Mockery::type(Closure::class))
            ->andReturnUsing(function($request, $next){
                echo '1';
                $response = $next($request);
                echo '1';
                return $response;
            })
            ->once();
        $second = Mockery::mock(\Abava\Routing\Contract\Middleware::class);
        $second->shouldReceive('handle')
              ->with($request, Mockery::type(Closure::class))
              ->andReturnUsing(function($request, $next){
                  echo '2';
                  $response = $next($request);
                  echo '2';
                  return $response;
              })
              ->once();
        $third = Mockery::mock(\Abava\Routing\Contract\Middleware::class);
        $third->shouldReceive('handle')
              ->with($request, Mockery::type(Closure::class))
              ->andReturnUsing(function($request, $next){
                  echo '3';
                  $response = $next($request);
                  echo '3';
                  return $response;
              })
              ->once();
        $last = function(){
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
                /** @var \Abava\Routing\Contract\Middleware $middleware */
                return $middleware->handle($request, $next);
            };
        }

        $this->expectOutputString('123last321');
        $response = $next($request);
        $this->assertSame(['third', 'second', 'first'], $names);
        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $response);
    }

    public function testReverseOrder()
    {
        $middleware = Mockery::mock(\Abava\Routing\Contract\Middleware::class);
        $this->collector->pushMiddleware('first', $middleware);
        $this->collector->rewind();
        $this->collector->pushMiddleware('second', $middleware);
        $middlewares = array_keys(iterator_to_array($this->collector, true));
        // Middlewares must be returned (traversed) in reversed order
        $this->assertSame(['second','first'], $middlewares);
    }

    public function testPushBeforeInReversedMode()
    {
        $this->collector->pushMiddleware('middleware', function(){});
        $this->collector->rewind();

        $this->expectException(RuntimeException::class);

        $this->collector->pushBefore('middleware', 'another', function(){});
    }

    public function testPushAfterInReversedMode()
    {
        $this->collector->pushMiddleware('middleware', function(){});
        $this->collector->rewind();

        $this->expectException(RuntimeException::class);

        $this->collector->pushAfter('middleware', 'another', function(){});
    }

    public function tearDown()
    {
        Mockery::close();
    }

}
