<?php

use Abava\Routing\Dispatcher\Factory\GroupCountBasedFactory;
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
        $factory = new GroupCountBasedFactory();
        $dispatcher = $factory->make([[], []]);
        $this->assertInstanceOf(GroupCountBased::class, $dispatcher);
    }

}
