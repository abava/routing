<?php

use PHPUnit\Framework\TestCase;

/**
 * Class CollectorTraitTest
 */
class CollectorTraitTest extends TestCase
{
    /**
     * @test
     */
    public function canCreateGetRoute()
    {
        $collector = new class
        {
            use \Abava\Routing\CollectorTrait;
        };
        /** @var \Abava\Routing\Route $route */
        $route = $collector->get('/url', 'handler');
        $this->assertSame(['GET'], $route->getMethods());
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handler', $route->getCallable());
    }

    /**
     * @test
     */
    public function canCreatePostRoute()
    {
        $collector = new class
        {
            use \Abava\Routing\CollectorTrait;
        };
        /** @var \Abava\Routing\Route $route */
        $route = $collector->post('/url', 'handler');
        $this->assertSame(['POST'], $route->getMethods());
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handler', $route->getCallable());
    }

    /**
     * @test
     */
    public function canCreatePutRoute()
    {
        $collector = new class
        {
            use \Abava\Routing\CollectorTrait;
        };
        /** @var \Abava\Routing\Route $route */
        $route = $collector->put('/url', 'handler');
        $this->assertSame(['PUT'], $route->getMethods());
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handler', $route->getCallable());
    }

    /**
     * @test
     */
    public function canCreatePatchRoute()
    {
        $collector = new class
        {
            use \Abava\Routing\CollectorTrait;
        };
        /** @var \Abava\Routing\Route $route */
        $route = $collector->patch('/url', 'handler');
        $this->assertSame(['PATCH'], $route->getMethods());
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handler', $route->getCallable());
    }

    /**
     * @test
     */
    public function canCreateDeleteRoute()
    {
        $collector = new class
        {
            use \Abava\Routing\CollectorTrait;
        };
        /** @var \Abava\Routing\Route $route */
        $route = $collector->delete('/url', 'handler');
        $this->assertSame(['DELETE'], $route->getMethods());
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handler', $route->getCallable());
    }

    /**
     * @test
     */
    public function canCreateHeadRoute()
    {
        $collector = new class
        {
            use \Abava\Routing\CollectorTrait;
        };
        /** @var \Abava\Routing\Route $route */
        $route = $collector->head('/url', 'handler');
        $this->assertSame(['HEAD'], $route->getMethods());
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handler', $route->getCallable());
    }

    /**
     * @test
     */
    public function canCreateOptionsRoute()
    {
        $collector = new class
        {
            use \Abava\Routing\CollectorTrait;
        };
        /** @var \Abava\Routing\Route $route */
        $route = $collector->options('/url', 'handler');
        $this->assertSame(['OPTIONS'], $route->getMethods());
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handler', $route->getCallable());
    }
    
}
