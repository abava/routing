<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Routing\Delegate;
use Venta\Contracts\Routing\ImmutableRouteCollection;
use Venta\Contracts\Routing\MiddlewarePipeline;
use Venta\Contracts\Routing\MiddlewarePipelineFactory;
use Venta\Contracts\Routing\RequestRouteCollectionFactory;
use Venta\Contracts\Routing\Route;
use Venta\Contracts\Routing\RouteDispatcher;
use Venta\Contracts\Routing\RouteDispatcherFactory;
use Venta\Contracts\Routing\RouteMatcher;

class RouterSpec extends ObjectBehavior
{

    function let(
        RouteMatcher $matcher,
        MiddlewarePipelineFactory $pipelineFactory,
        ImmutableRouteCollection $routes,
        RouteDispatcherFactory $dispatcherFactory,
        RequestRouteCollectionFactory $requestRouteCollectionFactory
    ) {
        $this->beConstructedWith($dispatcherFactory, $matcher, $pipelineFactory, $routes,
            $requestRouteCollectionFactory);
    }

    function it_is_initializable()
    {
        $this->shouldImplement(Delegate::class);
    }

    function it_matches_and_dispatches_route(
        RouteMatcher $matcher,
        MiddlewarePipelineFactory $pipelineFactory,
        ImmutableRouteCollection $routes,
        RouteDispatcherFactory $dispatcherFactory,
        RequestRouteCollectionFactory $requestRouteCollectionFactory,
        Route $route,
        MiddlewarePipeline $pipeline,
        RouteDispatcher $routeDispatcher,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $matcher->match($request, $routes)->willReturn($route);
        $route->middlewares()->willReturn(['middleware']);
        $pipelineFactory->create(['middleware'])->willReturn($pipeline->getWrappedObject());
        $dispatcherFactory->create($route)->willReturn($routeDispatcher);
        $pipeline->process($request, $routeDispatcher)->willReturn($response);
        $requestRouteCollectionFactory->create($routes, $request)->willReturn($routes);
        $this->next($request)->shouldBe($response);
    }
}
