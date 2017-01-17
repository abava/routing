<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Adr\Responder;
use Venta\Contracts\Container\Invoker;
use Venta\Contracts\Routing\Delegate;
use Venta\Contracts\Routing\Route;

class RouteDispatcherSpec extends ObjectBehavior
{
    function let(Route $route, Invoker $invoker)
    {
        $this->beConstructedWith($invoker, $route);
    }

    function it_calls_responder_only_when_domain_is_not_callable(
        Invoker $invoker,
        Route $route,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $route->domain()->willReturn('');
        $route->responder()->willReturn(Responder::class);
        $invoker->isCallable('')->willReturn(false);
        $invoker->call([Responder::class, 'run'], [$request, null])->willReturn($response);
        $request->withAttribute('route', $route)->willReturn($request);

        $this->next($request)->shouldBe($response);
    }

    function it_is_initializable()
    {
        $this->shouldImplement(Delegate::class);
    }
}
