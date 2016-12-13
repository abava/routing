<?php

namespace spec\Venta\Routing;

use FastRoute\RouteCollector;
use PhpSpec\ObjectBehavior;
use Venta\Contracts\Routing\Route;

class RouteParserSpec extends ObjectBehavior
{

    function let(RouteCollector $collector)
    {
        $this->beConstructedWith($collector);
    }

    function it_is_initializable()
    {
        $this->shouldImplement(\Venta\Contracts\Routing\RouteParser::class);
    }

    function it_parses_routes(RouteCollector $collector, Route $route)
    {
        $route->methods()->willReturn(['GET']);
        $route->path()->willReturn('/url');
        $collector->addRoute(['GET'], '/url', $route)->shouldBeCalled();
        $collector->getData()->willReturn(['parse result']);

        $this->parse([$route])->shouldBe(['parse result']);
    }

}
