<?php

namespace spec\Venta\Routing\Factory;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Routing\ImmutableRouteCollection;
use Venta\Routing\RequestRouteCollection;

class RequestRouteCollectionFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldImplement(\Venta\Contracts\Routing\RequestRouteCollectionFactory::class);
    }

    function it_returns_request_route_collection(ServerRequestInterface $request, ImmutableRouteCollection $collection)
    {
        $this->create($collection, $request)->shouldBeAnInstanceOf(RequestRouteCollection::class);
    }
}
