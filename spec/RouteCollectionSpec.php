<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Venta\Contracts\Routing\Route;
use Venta\Contracts\Routing\RouteGroup;

class RouteCollectionSpec extends ObjectBehavior
{

    function it_can_add_group(RouteGroup $group, Route $route)
    {
        $group->all()->willReturn([$route]);
        $this->addGroup($group);
        $this->all()->shouldContain($route);
    }

    function it_can_add_route(Route $route)
    {
        $this->addRoute($route);
        $this->all()->shouldContain($route);
    }

    function it_implements_contract()
    {
        $this->shouldHaveType(\Venta\Contracts\Routing\ImmutableRouteCollection::class);
    }

}
