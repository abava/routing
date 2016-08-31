<?php

use Abava\Routing\Dispatcher\Factory\GroupCountBasedDispatcherFactory;
use FastRoute\Dispatcher\GroupCountBased;
use PHPUnit\Framework\TestCase;

/**
 * Class GroupCountBasedFactoryTest
 */
class GroupCountBasedFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function canMake()
    {
        $factory = new GroupCountBasedDispatcherFactory();
        $dispatcher = $factory->make([[], []]);
        $this->assertInstanceOf(GroupCountBased::class, $dispatcher);
    }

}
