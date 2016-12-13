<?php

namespace spec\Venta\Routing;

use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Venta\Contracts\Routing\Route;

class RouteSpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedWith(['GET'], 'url', 'responder');
    }

    function it_compiles_path()
    {
        $this->beConstructedWith(['GET'], 'url/{var1}/{var2}', 'responder');
        $this->compilePath(['var1' => 'value1', 'var2' => 'value2'])->shouldBe('/url/value1/value2');
        $this->shouldThrow(InvalidArgumentException::class)->during('compilePath', [['var1' => 'value1']]);
    }

    function it_compiles_path_with_optional_segments()
    {
        $this->beConstructedWith(['GET'], 'url/{var1}[/{var2}[/{var3}]]', 'responder');
        $this->compilePath(['var1' => 'value1'])->shouldBe('/url/value1');
        $this->compilePath(['var1' => 'value1', 'var2' => 'value2'])->shouldBe('/url/value1/value2');
        $this->compilePath(['var1' => 'value1', 'var2' => 'value2', 'var3' => 'value3'])
             ->shouldBe('/url/value1/value2/value3');
        $this->shouldThrow(InvalidArgumentException::class)->during('compilePath', []);
    }

    function it_compiles_path_with_regex_constraints()
    {
        $this->beConstructedWith(['GET'], 'url/{var1:\d+}/{var2:[a-z]+}', 'responder');
        $this->compilePath(['var1' => '123', 'var2' => 'abc'])->shouldBe('/url/123/abc');
        $this->shouldThrow(InvalidArgumentException::class)->during('compilePath', [['var1' => '123', 'var2' => '1&']]);
    }

    function it_has_immutable_domain()
    {
        $this->withDomain('domain')->domain()->shouldBe('domain');
        $this->domain()->shouldBe('');
    }

    function it_has_immutable_host()
    {
        $route = $this->withHost('host');
        $route->host()->shouldBe('host');
        $this->host()->shouldBe('');
    }

    function it_has_immutable_input()
    {
        $route = $this->withInput('input');
        $route->input()->shouldBe('input');
        $this->input()->shouldBe('');
    }

    function it_has_immutable_name()
    {
        $route = $this->withName('named');
        $route->name()->shouldBe('named');
        $this->name()->shouldBe('');
    }

    function it_has_immutable_path()
    {
        $route = $this->withPath('/qwerty');
        $route->path()->shouldBe('/qwerty');
        $this->path()->shouldBe('/url');
    }

    function it_has_immutable_scheme()
    {
        $route = $this->secure();
        $route->scheme()->shouldBe('https');
        $this->scheme()->shouldBe('');
    }

    function it_has_middleware_list()
    {
        $route = $this->withMiddleware('middleware');
        $route->middlewares()->shouldContain('middleware');
        $this->middlewares()->shouldBe([]);
    }

    function it_is_initializable()
    {
        $this->shouldImplement(Route::class);
        $this->path()->shouldBe('/url');
        $this->responder()->shouldBe('responder');
        $this->methods()->shouldContain('GET');
        $this->host()->shouldBe('');
        $this->scheme()->shouldBe('');
        $this->name()->shouldBe('');
        $this->input()->shouldBe('');
        $this->domain()->shouldBe('');
    }

}
