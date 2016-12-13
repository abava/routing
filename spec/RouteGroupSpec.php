<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Venta\Contracts\Routing\RouteGroup;
use Venta\Routing\Route;

class RouteGroupSpec extends ObjectBehavior
{
    function let()
    {
        $this->addRoute(new Route(['GET'], '/url', 'responder'));
    }

    function it_collects_route_by_callback()
    {
        $route = new Route(['GET'], '/url', 'responder');
        $group = $this::collect(function (RouteGroup $group) use ($route) {
            $group->addRoute($route);
        })->shouldBeAnInstanceOf(RouteGroup::class);

        // todo: implement with specs
        assert(in_array($route, $group->getRoutes()));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RouteGroup::class);
    }

    function it_sets_host_on_route()
    {
        $this->setHost('host')->shouldBe($this);
        $routes = $this->all();
        $routes[0]->host()->shouldBe('host');
    }

    function it_sets_prefix_on_route()
    {
        $this->setPrefix('prefix')->shouldBe($this);
        $routes = $this->all();
        $routes[0]->path()->shouldBe('/prefix/url');
    }

    function it_sets_scheme_on_route()
    {
        $this->setScheme('https')->shouldBe($this);
        $routes = $this->all();
        $routes[0]->scheme()->shouldBe('https');
    }

}
