<?php

use Venta\Routing\Dispatcher\Factory\GroupCountBasedDispatcherFactory;
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
        $dispatcher = $factory->create([[], []]);
        $this->assertInstanceOf(GroupCountBased::class, $dispatcher);
    }

}
