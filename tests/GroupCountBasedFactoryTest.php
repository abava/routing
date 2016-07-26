<?php

use Abava\Routing\Dispatcher\Factory\GroupCountBasedFactory;
use FastRoute\Dispatcher\GroupCountBased;

class GroupCountBasedFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testMake()
    {
        $factory = new GroupCountBasedFactory();
        $dispatcher = $factory->make([[],[]]);
        $this->assertInstanceOf(GroupCountBased::class, $dispatcher);
    }

}
