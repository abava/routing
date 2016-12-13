<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\UriInterface;
use Venta\Contracts\Http\Request;
use Venta\Contracts\Routing\ImmutableRouteCollection;
use Venta\Contracts\Routing\Route;
use Venta\Contracts\Routing\UrlGenerator as UrlGeneratorContract;

class UrlGeneratorSpec extends ObjectBehavior
{
    function let(Request $request, ImmutableRouteCollection $routeCollection, UriInterface $uri)
    {
        $this->beConstructedWith($request, $routeCollection, $uri);
    }

    function it_generates_url_to_current_route(
        Request $request,
        Route $route,
        UriInterface $uri,
        UriInterface $requestUri
    ) {
        $request->route()->willReturn($route);
        $request->getUri()->willReturn($requestUri);
        $requestUri->getPort()->willReturn(8080);

        $route->scheme()->willReturn('http');
        $route->host()->willReturn('example.com');
        $route->compilePath(['key' => 'value'])->willReturn('/url');

        $uri->withScheme('http')->willReturn($uri);
        $uri->withHost('example.com')->willReturn($uri);
        $uri->withPath('/url')->willReturn($uri);
        $uri->withQuery('param=val')->willReturn($uri);
        $uri->withPort(8080)->willReturn($uri);

        $result = $this->toCurrent(['key' => 'value'], ['param' => 'val']);
        $result->shouldBe($uri);

        $uri->withScheme('http')->shouldHaveBeenCalled();
        $uri->withHost('example.com')->shouldHaveBeenCalled();
        $uri->withPath('/url')->shouldHaveBeenCalled();
        $uri->withPort(8080)->shouldHaveBeenCalled();
    }

    function it_generates_url_to_named_route(
        ImmutableRouteCollection $routeCollection,
        Route $route,
        UriInterface $uri,
        Request $request,
        UriInterface $requestUri
    ) {
        $routeCollection->findByName('name')->willReturn($route);
        $route->scheme()->willReturn('http');
        $route->host()->willReturn('example.com');
        $route->compilePath(['key' => 'value'])->willReturn('/url');

        $request->getUri()->willReturn($requestUri);
        $requestUri->getPort()->willReturn(80);

        $uri->withScheme('http')->willReturn($uri);
        $uri->withHost('example.com')->willReturn($uri);
        $uri->withPath('/url')->willReturn($uri);
        $uri->withQuery('param=val')->willReturn($uri);

        $result = $this->toRoute('name', ['key' => 'value'], ['param' => 'val']);
        $result->shouldBe($uri);

        $uri->withScheme('http')->shouldHaveBeenCalled();
        $uri->withHost('example.com')->shouldHaveBeenCalled();
        $uri->withPath('/url')->shouldHaveBeenCalled();
    }

    function it_is_initializable()
    {
        $this->shouldImplement(UrlGeneratorContract::class);
    }
}
