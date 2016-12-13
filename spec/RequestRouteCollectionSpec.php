<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Venta\Contracts\Routing\ImmutableRouteCollection;
use Venta\Routing\Route;

class RequestRouteCollectionSpec extends ObjectBehavior
{
    function let(ServerRequestInterface $request, ImmutableRouteCollection $collection)
    {
        $this->beConstructedWith($request, $collection);
    }

    function it_calls_decorated_collection_on_get_goutes(ImmutableRouteCollection $collection)
    {
        $collection->all()->shouldBeCalled();
        $this->all()->shouldBe([]);
    }

    function it_filters_decorated_collection_on_get_goutes(
        ImmutableRouteCollection $collection,
        ServerRequestInterface $request,
        UriInterface $uri
    ) {
        $route1 = (new Route(['GET'], '/url1', 'responder1'))->withHost('localhost');
        $route2 = (new Route(['GET'], '/url2', 'responder2'))->secure();
        $route3 = new Route(['GET'], '/url3', 'responder3');
        $collection->all()->willReturn([$route1, $route2, $route3]);
        $request->getUri()->willReturn($uri);
        $uri->getHost()->willReturn('host');
        $uri->getScheme()->willReturn('http');
        $this->all()->shouldBe([$route3]);
    }

    function it_is_initializable()
    {
        $this->shouldImplement(ImmutableRouteCollection::class);
    }

}
