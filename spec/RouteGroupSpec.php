<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Venta\Routing\Route;

class RouteGroupSpec extends ObjectBehavior
{
    function let()
    {
        $this->addRoute(new Route(['GET'], '/url', 'handler'));
    }

    function it_collects_route_by_callback()
    {
        $route = new Route(['GET'], '/url', 'handler');
        $group = $this->collect(function (\Venta\Contracts\Routing\RouteGroup $group) use ($route) {
            $group->addRoute($route);
        })->shouldBeAnInstanceOf(\Venta\Contracts\Routing\RouteGroup::class);
        assert(in_array($route, $group->getRoutes()));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(\Venta\Contracts\Routing\RouteGroup::class);
    }

    function it_sets_host_on_route()
    {
        $this->setHost('host')->shouldBe($this);
        $routes = $this->getRoutes();
        $routes[0]->getHost()->shouldBe('host');
    }

    function it_sets_prefix_on_route()
    {
        $this->setPrefix('prefix')->shouldBe($this);
        $routes = $this->getRoutes();
        $routes[0]->getPath()->shouldBe('/prefix/url');
    }

    function it_sets_scheme_on_route()
    {
        $this->setScheme('https')->shouldBe($this);
        $routes = $this->getRoutes();
        $routes[0]->getScheme()->shouldBe('https');
    }

}
