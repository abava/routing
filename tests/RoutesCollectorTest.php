<?php declare(strict_types = 1);

class RoutesCollectorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testGetRoutesCollectionFromGenerator()
    {
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\FastRoute\RouteParser $parser */
        $parser = $this->getMockBuilder(FastRoute\RouteParser::class)->getMock();

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\FastRoute\DataGenerator $generator */
        $generator = $this->getMockBuilder(FastRoute\DataGenerator::class)->getMock();
        $generator->method('getData')->willReturn(['routes']);

        $collector = new \Abava\Routing\RoutesCollector($parser, $generator);
        $this->assertEquals(['routes'], $collector->getRoutesCollection());
    }

    /**
     * @test
     */
    public function testAddRoutes()
    {
        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|\FastRoute\RouteParser $parser */
        $parser = $this->getMockBuilder(FastRoute\RouteParser::class)->getMock();
        $parser->method('parse')->with('/url')->willReturn(['data']);

        /** @var PHPUnit_Framework_MockObject_Builder_InvocationMocker|PHPUnit_Framework_MockObject_MockObject|\FastRoute\DataGenerator $generator */
        $generator = $this->getMockBuilder(FastRoute\DataGenerator::class)->getMock();
        $generator->method('getData')->willReturn(['routes']);
        $generator->expects($this->exactly(7))->method('addRoute')
            ->withConsecutive(
                ['GET', 'data', 'handle'],
                ['HEAD', 'data', 'handle'],
                ['PUT', 'data', 'handle'],
                ['POST', 'data', 'handle'],
                ['OPTIONS', 'data', 'handle'],
                ['PATCH', 'data', 'handle'],
                ['DELETE', 'data', 'handle']
            );

        $collector = new \Abava\Routing\RoutesCollector($parser, $generator);
        $collector->get('/url','handle');
        $collector->put('/url','handle');
        $collector->post('/url','handle');
        $collector->options('/url','handle');
        $collector->patch('/url','handle');
        $collector->delete('/url','handle');
        $collector->getRoutesCollection();
        $this->assertEquals(['routes'], $collector->getRoutesCollection());
    }

}
