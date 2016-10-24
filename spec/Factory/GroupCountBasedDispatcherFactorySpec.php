<?php

namespace spec\Venta\Routing\Factory;

use FastRoute\Dispatcher\GroupCountBased;
use PhpSpec\ObjectBehavior;

class GroupCountBasedDispatcherFactorySpec extends ObjectBehavior
{
    function it_creates_group_count_based_dispatcher()
    {
        $this->create([[], []])->shouldBeAnInstanceOf(GroupCountBased::class);
    }

    function it_is_initializable()
    {
        $this->shouldImplement(\Venta\Contracts\Routing\FastrouteDispatcherFactory::class);
    }
}
