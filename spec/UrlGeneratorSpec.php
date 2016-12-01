<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\UriInterface;
use Venta\Contracts\Http\Request;
use Venta\Contracts\Routing\Route;
use Venta\Contracts\Routing\RouteCollection;
use Venta\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Venta\Routing\UrlGenerator;

class UrlGeneratorSpec extends ObjectBehavior
{
    function let(Request $request, RouteCollection $routeCollection, UriInterface $uri)
    {
        $this->beConstructedWith($request, $routeCollection, $uri);
    }

    function it_generates_url_to_current_route(
        Request $request,
        Route $route,
        UriInterface $uri
    ) {
        $request->getRoute()->willReturn($route);
        $route->getScheme()->willReturn('http');
        $route->getHost()->willReturn('example.com');
        $route->compilePath(['key' => 'value'])->willReturn('/url');

        $uri->withScheme('http')->willReturn($uri);
        $uri->withHost('example.com')->willReturn($uri);
        $uri->withPath('/url')->willReturn($uri);
        $uri->withQuery('param=val')->willReturn($uri);

        $result = $this->toCurrent(['key' => 'value'], ['param' => 'val']);
        $result->shouldBe($uri);

        $uri->withScheme('http')->shouldHaveBeenCalled();
        $uri->withHost('example.com')->shouldHaveBeenCalled();
        $uri->withPath('/url')->shouldHaveBeenCalled();

    }

    function it_generates_url_to_named_route(
        RouteCollection $routeCollection,
        Route $route,
        UriInterface $uri
    ) {
        $routeCollection->findByName('name')->willReturn($route);
        $route->getScheme()->willReturn('http');
        $route->getHost()->willReturn('example.com');
        $route->compilePath(['key' => 'value'])->willReturn('/url');

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
        $this->shouldHaveType(UrlGenerator::class);
        $this->shouldImplement(UrlGeneratorContract::class);
    }
}
