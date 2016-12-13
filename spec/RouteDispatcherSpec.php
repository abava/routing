<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Adr\Responder;
use Venta\Contracts\Container\Container;
use Venta\Contracts\Routing\Delegate;
use Venta\Contracts\Routing\Route;

class RouteDispatcherSpec extends ObjectBehavior
{
    function let(Route $route, Container $container)
    {
        $this->beConstructedWith($route, $container);
    }

    function it_calls_responder_only_when_domain_is_not_callable(
        Container $container,
        Route $route,
        ServerRequestInterface $request,
        ResponseInterface $response,
        Responder $responder
    ) {
        $route->domain()->willReturn('');
        $route->responder()->willReturn(Responder::class);
        $container->isCallable('')->willReturn(false);
        $container->get(Responder::class)->willReturn($responder);
        $responder->run($request, null)->willReturn($response);
        $request->withAttribute('route', $route)->willReturn($request);

        $this->next($request)->shouldBe($response);
    }

    function it_is_initializable()
    {
        $this->shouldImplement(Delegate::class);
    }
}
