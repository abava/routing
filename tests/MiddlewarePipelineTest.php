<?php

class MiddlewarePipelineTest extends PHPUnit_Framework_TestCase
{


    public function testHandle()
    {
        $this->expectOutputString('12321');

        $middleware1 = function($request, $next){
            echo '1';
            $response = $next($request);
            echo '1';
            return $response;
        };

        $middleware2 = function($request, $next){
            echo '2';
            $response = $next($request);
            echo '2';
            return $response;
        };

        $mock1 = Mockery::mock(\Abava\Routing\Contract\Middleware::class);
        $mock1->shouldReceive('handle')->andReturnUsing($middleware1);
        $mock2 = Mockery::mock(\Abava\Routing\Contract\Middleware::class);
        $mock2->shouldReceive('handle')->andReturnUsing($middleware2);

        $collector = Mockery::mock(\Abava\Routing\Middleware\Collector::class);
        $collector->shouldReceive('rewind')->once();
        $collector->shouldReceive('next');
        $collector->shouldReceive('key');
        $collector->shouldReceive('current')->andReturn(
            $mock2, // middlewares are returned in reversed order
            $mock1
        );
        $collector->shouldReceive('valid')->andReturn(true, true, false);
        $pipeline = new \Abava\Routing\Middleware\Pipeline($collector);
        $pipeline->handle(
            Mockery::mock(\Psr\Http\Message\RequestInterface::class),
            function(){
                echo '3';
                return Mockery::mock(\Psr\Http\Message\ResponseInterface::class);
            }
        );
    }

    public function tearDown()
    {
        Mockery::close();
    }

}
