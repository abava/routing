<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;

class RouteSpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedWith(['GET'], 'url', 'responder');
    }

    function it_has_immutable_domain()
    {
        $route = $this->withDomain('domain');
        $route->getDomain()->shouldBe('domain');
        $this->getDomain()->shouldBe('');
    }

    function it_has_immutable_host()
    {
        $route = $this->withHost('host');
        $route->getHost()->shouldBe('host');
        $this->getHost()->shouldBe('');
    }

    function it_has_immutable_input()
    {
        $route = $this->withInput('input');
        $route->getInput()->shouldBe('input');
        $this->getInput()->shouldBe('');
    }
    
    function it_has_immutable_name()
    {
        $route = $this->withName('named');
        $route->getName()->shouldBe('named');
        $this->getName()->shouldBe('');
    }

    function it_has_immutable_scheme()
    {
        $route = $this->withScheme('https');
        $route->getScheme()->shouldBe('https');
        $this->getScheme()->shouldBe('');
    }

    function it_has_middleware_list()
    {
        $route = $this->withMiddleware('middleware');
        $route->getMiddlewares()->shouldContain('middleware');
        $this->getMiddlewares()->shouldBe([]);
    }

    function it_implements_route_contract()
    {
        $this->shouldHaveType(\Venta\Contracts\Routing\Route::class);
    }

    function it_is_initializable()
    {
        $this->getPath()->shouldBe('/url');
        $this->getResponder()->shouldBe('responder');
        $this->getMethods()->shouldContain('GET');
        $this->getHost()->shouldBe('');
        $this->getScheme()->shouldBe('');
        $this->getName()->shouldBe('');
        $this->getInput()->shouldBe('');
        $this->getDomain()->shouldBe('');
    }


}
