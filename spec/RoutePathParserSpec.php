<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Venta\Routing\RoutePathParser;

class RoutePathParserSpec extends ObjectBehavior
{
    function it_allows_to_add_regex_aliases()
    {
        $this::addRegexAlias('alias', 'regex');
        $this::replaceRegexAliases('{var:alias}')->shouldReturn('{var:regex}');
    }

    function it_does_not_replace_undefined_regex_aliases()
    {
        $this::replaceRegexAliases('{var:\d+}')->shouldReturn('{var:\d+}');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RoutePathParser::class);
    }

    function it_replaces_alphanum_alias()
    {
        $this::replaceRegexAliases('{var:alphanum}')->shouldReturn('{var:[a-zA-Z0-9-_]+}');
    }

    function it_replaces_number_alias()
    {
        $this::replaceRegexAliases('{var:number}')->shouldReturn('{var:[0-9]+}');
    }

    function it_replaces_slug_alias()
    {
        $this::replaceRegexAliases('{var:slug}')->shouldReturn('{var:[a-z0-9-]+}');
    }

    function it_replaces_word_alias()
    {
        $this::replaceRegexAliases('{var:word}')->shouldReturn('{var:[a-zA-Z]+}');
    }

}
