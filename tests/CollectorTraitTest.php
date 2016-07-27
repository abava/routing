<?php

class CollectorTraitTest extends PHPUnit_Framework_TestCase
{

    public function testGet()
    {
        $collector = new class {
            use \Abava\Routing\CollectorTrait;
        };
        /** @var \Abava\Routing\Route $route */
        $route = $collector->get('/url', 'handler');
        $this->assertSame(['GET'], $route->getMethods());
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handler', $route->getCallable());
    }
    
    public function testPost()
    {
        $collector = new class {
            use \Abava\Routing\CollectorTrait;
        };
        /** @var \Abava\Routing\Route $route */
        $route = $collector->post('/url', 'handler');
        $this->assertSame(['POST'], $route->getMethods());
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handler', $route->getCallable());
    }

    public function testPut()
    {
        $collector = new class {
            use \Abava\Routing\CollectorTrait;
        };
        /** @var \Abava\Routing\Route $route */
        $route = $collector->put('/url', 'handler');
        $this->assertSame(['PUT'], $route->getMethods());
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handler', $route->getCallable());
    }

    public function testPatch()
    {
        $collector = new class {
            use \Abava\Routing\CollectorTrait;
        };
        /** @var \Abava\Routing\Route $route */
        $route = $collector->patch('/url', 'handler');
        $this->assertSame(['PATCH'], $route->getMethods());
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handler', $route->getCallable());
    }

    public function testDelete()
    {
        $collector = new class {
            use \Abava\Routing\CollectorTrait;
        };
        /** @var \Abava\Routing\Route $route */
        $route = $collector->delete('/url', 'handler');
        $this->assertSame(['DELETE'], $route->getMethods());
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handler', $route->getCallable());
    }

    public function testHead()
    {
        $collector = new class {
            use \Abava\Routing\CollectorTrait;
        };
        /** @var \Abava\Routing\Route $route */
        $route = $collector->head('/url', 'handler');
        $this->assertSame(['HEAD'], $route->getMethods());
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handler', $route->getCallable());
    }

    public function testOptions()
    {
        $collector = new class {
            use \Abava\Routing\CollectorTrait;
        };
        /** @var \Abava\Routing\Route $route */
        $route = $collector->options('/url', 'handler');
        $this->assertSame(['OPTIONS'], $route->getMethods());
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handler', $route->getCallable());
    }
    
}
