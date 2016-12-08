<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Venta\Contracts\Routing\Route;
use Venta\Contracts\Routing\RoutePathProcessor as RoutePathProcessorContract;
use Venta\Contracts\Routing\RouteProcessor;

class RoutePathProcessorSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldImplement(RoutePathProcessorContract::class);
        $this->shouldImplement(RouteProcessor::class);
    }

    function it_processes(Route $route)
    {
        $route->getPath()->willReturn('/url/{id}');
        $route->withPath(Argument::type('string'))->willReturn($route);

        $this->addPattern('id', '[0-9]+')->shouldBeAnInstanceOf(RoutePathProcessorContract::class);
        $this->process($route)->shouldBe($route);

        $route->withPath('/url/{id:[0-9]+}')->shouldHaveBeenCalled();
    }

    function it_processes_optional_placeholders(Route $route)
    {
        $route->getPath()->willReturn('/url/{?id}/segment/{?optional}');
        $route->withPath(Argument::type('string'))->willReturn($route);

        $this->addPattern('id', '[0-9]+')->shouldBeAnInstanceOf(RoutePathProcessorContract::class);
        $this->process($route)->shouldBe($route);

        $route->withPath('/url/[{id:[0-9]+}/segment/[{optional}]]')->shouldHaveBeenCalled();
    }
}
