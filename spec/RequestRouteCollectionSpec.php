<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Venta\Contracts\Routing\Route;
use Venta\Contracts\Routing\RouteCollection;
use Venta\Contracts\Routing\RouteGroup;

class RequestRouteCollectionSpec extends ObjectBehavior
{
    function let(ServerRequestInterface $request, RouteCollection $collection)
    {
        $this->beConstructedWith($request, $collection);
    }

    function it_calls_decorated_collection_on_add_group(RouteCollection $collection, RouteGroup $group)
    {
        $collection->addGroup($group)->shouldBeCalled();
        $this->addGroup($group)->shouldBe($this);
    }

    function it_calls_decorated_collection_on_add_route(RouteCollection $collection, Route $route)
    {
        $collection->addRoute($route)->shouldBeCalled();
        $this->addRoute($route)->shouldBe($this);
    }

    function it_calls_decorated_collection_on_get_goutes(RouteCollection $collection)
    {
        $collection->getRoutes()->shouldBeCalled();
        $this->getRoutes()->shouldBe([]);
    }

    function it_filters_decorated_collection_on_get_goutes(
        RouteCollection $collection,
        ServerRequestInterface $request,
        UriInterface $uri
    ) {
        $route1 = (new \Venta\Routing\Route(['GET'], '/url1', 'handler1'))->withHost('localhost');
        $route2 = (new \Venta\Routing\Route(['GET'], '/url2', 'handler2'))->withScheme('https');
        $route3 = new \Venta\Routing\Route(['GET'], '/url3', 'handler3');
        $collection->getRoutes()->willReturn([$route1, $route2, $route3]);
        $request->getUri()->willReturn($uri);
        $uri->getHost()->willReturn('host');
        $uri->getScheme()->willReturn('http');
        $this->getRoutes()->shouldBe([$route3]);
    }

    function it_is_initializable()
    {
        $this->shouldImplement(RouteCollection::class);
    }

}
