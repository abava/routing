<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Venta\Contracts\Routing\Route;
use Venta\Contracts\Routing\RouteGroup;

class RouteCollectionSpec extends ObjectBehavior
{

    function it_()
    {

    }

    function it_can_add_group(RouteGroup $group, Route $route)
    {
        $group->getRoutes()->willReturn([$route]);
        $this->addGroup($group);
        $this->getRoutes()->shouldContain($route);
    }

    function it_can_add_route(Route $route)
    {
        $this->addRoute($route);
        $this->getRoutes()->shouldContain($route);
    }

    function it_implements_contract()
    {
        $this->shouldHaveType(\Venta\Contracts\Routing\RouteCollection::class);
    }

//    function it()
//    {
//        $routes->addGroup(Group::collect(function($group){
//            $group->addRoute(new Route(['GET'], '/url', 'handler'));
//            $group->addRoute(new Route(['POST'], '/url', 'handler'));

//        })->setHost()->setScheme()->setPrefix());
//
//        $routes->addGroup((new Group(function($group){
//            $group->addRoute(new Route(['GET'], '/url', 'handler'));
//            $group->addRoute(new Route(['POST'], '/url', 'handler'));
//        }))->withHost()->withScheme()->withPrefix());
//    }

}
