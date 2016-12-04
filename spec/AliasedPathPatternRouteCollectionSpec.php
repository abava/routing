<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Venta\Contracts\Routing\MutableRouteCollection;
use Venta\Contracts\Routing\Route;

class AliasedPathPatternRouteCollectionSpec extends ObjectBehavior
{
    function let(MutableRouteCollection $routes)
    {
        $this->beConstructedWith($routes);
    }

    function it_is_initializable()
    {
        $this->shouldImplement(MutableRouteCollection::class);
    }

    function it_replaces_regex_placeholder_aliases(MutableRouteCollection $routes, Route $route)
    {
        $route->getPath()->willReturn('{id:number}');
        $route->withPath('{id:[0-9]+}')->willReturn($route)->shouldBeCalled();
        $routes->addRoute($route)->willReturn($routes)->shouldBeCalled();

        $this->addRoute($route)->shouldBeAnInstanceOf(MutableRouteCollection::class);
    }
}
