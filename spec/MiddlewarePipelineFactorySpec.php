<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Venta\Contracts\Container\Container;
use Venta\Contracts\Routing\Middleware;
use Venta\Contracts\Routing\MiddlewarePipelineFactory;
use Venta\Routing\MiddlewarePipeline;

class MiddlewarePipelineFactorySpec extends ObjectBehavior
{
    function let(Container $container)
    {
        $this->beConstructedWith($container);
    }

    function it_creates_pipeline_from_array(Container $container, Middleware $middleware)
    {
        $container->get('middleware')->willReturn($middleware);
        $this->create(['middleware'])->shouldBeLike(
            (new MiddlewarePipeline())->withMiddleware($middleware->getWrappedObject())
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MiddlewarePipelineFactory::class);
    }

}
