<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Venta\Contracts\Routing\Route;
use Venta\Contracts\Routing\RouteCollection;
use Venta\Contracts\Routing\RouteProcessor;

class ProcessingRouteCollectionSpec extends ObjectBehavior
{
    function let(RouteProcessor $processor, RouteCollection $collection)
    {
        $this->beConstructedWith($collection, $processor);
    }

    function it_calls_processor(RouteProcessor $processor, Route $route, RouteCollection $collection)
    {
        $processor->process($route)->willReturn($route);
        $collection->addRoute($route)->willReturn($collection);

        $this->addRoute($route);

        $processor->process($route)->shouldHaveBeenCalled();
    }

    function it_is_initializable()
    {
        $this->shouldImplement(RouteCollection::class);
    }
}
