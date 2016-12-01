<?php

namespace spec\Venta\Routing;

use FastRoute\Dispatcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Venta\Contracts\Routing\FastrouteDispatcherFactory;
use Venta\Contracts\Routing\Route;
use Venta\Contracts\Routing\RouteCollection;
use Venta\Contracts\Routing\RouteParser;
use Venta\Routing\Exception\MethodNotAllowedException;
use Venta\Routing\Exception\RouteNotFoundException;

class RouteMatcherSpec extends ObjectBehavior
{

    function let(RouteParser $parser, FastrouteDispatcherFactory $factory, Route $route, Dispatcher $dispatcher)
    {
        $parser->parse(Argument::containing($route->getWrappedObject()))->willReturn(['parsed']);
        $factory->create(['parsed'])->willReturn($dispatcher);
        $this->beConstructedWith($parser, $factory);
    }

    function it_is_initializable()
    {
        $this->shouldImplement(\Venta\Contracts\Routing\RouteMatcher::class);
    }

    function it_matches_route(
        ServerRequestInterface $request,
        RouteCollection $routeCollection,
        Route $route,
        UriInterface $uri,
        Dispatcher $dispatcher
    ) {
        $routeCollection->getRoutes()->willReturn([$route]);
        $request->getUri()->willReturn($uri);
        $uri->getPath()->willReturn('/url');
        $request->getMethod()->willReturn('GET');
        $dispatcher->dispatch('GET', '/url')->willReturn([Dispatcher::FOUND, $route, ['vars']]);
        $route->withVariables(['vars'])->willReturn($route);
        $this->match($request, $routeCollection)->shouldBe($route);
    }

    function it_throws_not_allowed_exception(
        ServerRequestInterface $request,
        RouteCollection $routeCollection,
        Route $route,
        UriInterface $uri,
        Dispatcher $dispatcher
    ) {
        $routeCollection->getRoutes()->willReturn([$route]);
        $request->getUri()->willReturn($uri);
        $uri->getPath()->willReturn('/url');
        $request->getMethod()->willReturn('GET');
        $dispatcher->dispatch('GET', '/url')->willReturn([Dispatcher::METHOD_NOT_ALLOWED, ['POST']]);
        $this->shouldThrow(MethodNotAllowedException::class)->match($request, $routeCollection);
    }

    function it_throws_not_found_exception(
        ServerRequestInterface $request,
        RouteCollection $routeCollection,
        Route $route,
        UriInterface $uri,
        Dispatcher $dispatcher
    ) {
        $routeCollection->getRoutes()->willReturn([$route]);
        $request->getUri()->willReturn($uri);
        $uri->getPath()->willReturn('/url');
        $request->getMethod()->willReturn('GET');
        $dispatcher->dispatch('GET', '/url')->willReturn([Dispatcher::NOT_FOUND]);
        $this->shouldThrow(RouteNotFoundException::class)->match($request, $routeCollection);
    }
}
