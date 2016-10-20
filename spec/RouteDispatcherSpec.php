<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Container\Container;
use Venta\Contracts\Routing\Delegate;
use Venta\Contracts\Routing\Route;

class RouteDispatcherSpec extends ObjectBehavior
{
    function let(Route $route, Container $container)
    {
        $this->beConstructedWith($route, $container);
    }

    function it_calls_route_handler(
        Container $container,
        Route $route,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $route->getHandler()->willReturn('handler');
        $container->call('handler', ['request' => $request])->willReturn($response);
        $this->next($request)->shouldBe($response);
    }

    function it_is_initializable()
    {
        $this->shouldImplement(Delegate::class);
    }
}
