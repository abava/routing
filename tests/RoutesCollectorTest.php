<?php declare(strict_types = 1);

class RoutesCollectorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testGetRoutesCollectionFromGenerator()
    {
        $parser = $this->getMockBuilder(FastRoute\RouteParser::class)->getMock();
        //$parser->method('parse')->with('')->willReturn([]);

        $generator = $this->getMockBuilder(FastRoute\DataGenerator::class)->getMock();
        $generator->method('getData')->willReturn(['routes']);

        $collector = new \Venta\Routing\RoutesCollector($parser, $generator);
        $this->assertEquals(['routes'], $collector->getRoutesCollection());
    }

    /**
     * @test
     */
    public function testAddRoutes()
    {
        $parser = $this->getMockBuilder(FastRoute\RouteParser::class)->getMock();
        $parser->method('parse')->with('/url')->willReturn(['data']);

        $generator = $this->getMockBuilder(FastRoute\DataGenerator::class)->getMock();
        $generator->method('getData')->willReturn(['routes']);
        $generator->expects($this->exactly(2))->method('addRoute')
            ->withConsecutive(
                ['GET', 'data', 'handle'],
                ['HEAD', 'data', 'handle']
            );

        $collector = new \Venta\Routing\RoutesCollector($parser, $generator);
        $collector->get('/url','handle');
        $collector->getRoutesCollection();
        $this->assertEquals(['routes'], $collector->getRoutesCollection());
    }

    /**
     * @test
     */
    public function testAddPostRoute()
    {
        $parser = $this->getMockBuilder(FastRoute\RouteParser::class)->getMock();
        $parser->method('parse')->with('/url')->willReturn(['data']);

        $generator = $this->getMockBuilder(FastRoute\DataGenerator::class)->getMock();
        $generator->method('getData')->willReturn(['routes']);
        $generator->method('addRoute')->with('POST', 'data', 'handle');

        $collector = new \Venta\Routing\RoutesCollector($parser, $generator);
        $collector->post('/url','handle');
        $collector->getRoutesCollection();
        $this->assertEquals(['routes'], $collector->getRoutesCollection());
    }

    /**
     * @test
     */
    public function testAddPatchRoute()
    {
        $parser = $this->getMockBuilder(FastRoute\RouteParser::class)->getMock();
        $parser->method('parse')->with('/url')->willReturn(['data']);

        $generator = $this->getMockBuilder(FastRoute\DataGenerator::class)->getMock();
        $generator->method('getData')->willReturn(['routes']);
        $generator->method('addRoute')->with('PATCH', 'data', 'handle');

        $collector = new \Venta\Routing\RoutesCollector($parser, $generator);
        $collector->patch('/url','handle');
        $collector->getRoutesCollection();
        $this->assertEquals(['routes'], $collector->getRoutesCollection());
    }

    /**
     * @test
     */
    public function testAddPutRoute()
    {
        $parser = $this->getMockBuilder(FastRoute\RouteParser::class)->getMock();
        $parser->method('parse')->with('/url')->willReturn(['data']);

        $generator = $this->getMockBuilder(FastRoute\DataGenerator::class)->getMock();
        $generator->method('getData')->willReturn(['routes']);
        $generator->method('addRoute')->with('PUT', 'data', 'handle');

        $collector = new \Venta\Routing\RoutesCollector($parser, $generator);
        $collector->put('/url','handle');
        $collector->getRoutesCollection();
        $this->assertEquals(['routes'], $collector->getRoutesCollection());
    }

    /**
     * @test
     */
    public function testAddOptionsRoute()
    {
        $parser = $this->getMockBuilder(FastRoute\RouteParser::class)->getMock();
        $parser->method('parse')->with('/url')->willReturn(['data']);

        $generator = $this->getMockBuilder(FastRoute\DataGenerator::class)->getMock();
        $generator->method('getData')->willReturn(['routes']);
        $generator->method('addRoute')->with('OPTIONS', 'data', 'handle');

        $collector = new \Venta\Routing\RoutesCollector($parser, $generator);
        $collector->options('/url','handle');
        $collector->getRoutesCollection();
        $this->assertEquals(['routes'], $collector->getRoutesCollection());
    }

    /**
     * @test
     */
    public function testAddDeleteRoute()
    {
        $parser = $this->getMockBuilder(FastRoute\RouteParser::class)->getMock();
        $parser->method('parse')->with('/url')->willReturn(['data']);

        $generator = $this->getMockBuilder(FastRoute\DataGenerator::class)->getMock();
        $generator->method('getData')->willReturn(['routes']);
        $generator->method('addRoute')->with('DELETE', 'data', 'handle');

        $collector = new \Venta\Routing\RoutesCollector($parser, $generator);
        $collector->delete('/url','handle');
        $collector->getRoutesCollection();
        $this->assertEquals(['routes'], $collector->getRoutesCollection());
    }

}
