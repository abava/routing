<?php

use PHPUnit\Framework\TestCase;

/**
 * Class GenericStrategyTest
 */
class GenericStrategyTest extends TestCase
{

    protected $container;

    protected $factory;

    protected $response;

    protected $route;

    public function setUp()
    {
        $this->container = Mockery::mock(\Venta\Contracts\Container\Container::class);
        $this->response = Mockery::mock(\Psr\Http\Message\ResponseInterface::class);
        $this->route = (new \Venta\Routing\Route(['GET'], '/url', 'controller@action'))
            ->withParameters(['param' => 'value']);
        $this->factory = Mockery::mock(\Venta\Http\Factory\ResponseFactory::class);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function canReturnJSONResponse()
    {
        $this->container->shouldReceive('call')
                        ->with($this->route->getCallable(), $this->route->getParameters())
                        ->andReturn(['foo' => 'bar'])
                        ->once();

        $response = Mockery::mock(\Venta\Http\JsonResponse::class);
        $this->factory->shouldReceive('createJsonResponse')->with(['foo' => 'bar'])->andReturn($response)->once();

        $strategy = new \Venta\Routing\Strategy\Generic($this->container, $this->factory);
        $result = $strategy->dispatch($this->route);

        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $result);
        $this->assertInstanceOf(\Venta\Http\JsonResponse::class, $result);
        $this->assertSame($response, $result);
    }

    /**
     * @test
     */
    public function canReturnResponseInterfaceResult()
    {
        $this->container->shouldReceive('call')
                        ->with($this->route->getCallable(), $this->route->getParameters())
                        ->andReturn($this->response)
                        ->once();
        $strategy = new \Venta\Routing\Strategy\Generic($this->container, $this->factory);
        $result = $strategy->dispatch($this->route);

        $this->assertSame($this->response, $result);
    }

    /**
     * @test
     */
    public function canReturnStringableResult()
    {
        $this->container->shouldReceive('call')
                        ->with($this->route->getCallable(), $this->route->getParameters())
                        ->andReturn(new class
                        {
                            public function __toString()
                            {
                                return 'string';
                            }
                        })
                        ->once();
        // todo check of can be replaced with contract
        $ventaResponse = Mockery::mock(\Venta\Http\Response::class);
        $ventaResponse->shouldReceive('append')->with('string')->andReturn($ventaResponse)->once();
        $this->factory->shouldReceive('new')->withNoArgs()->andReturn($ventaResponse);
        $this->factory->shouldReceive('createResponse')->withNoArgs()->andReturn($ventaResponse);
        $strategy = new \Venta\Routing\Strategy\Generic($this->container, $this->factory);
        $result = $strategy->dispatch($this->route);

        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $result);
    }

    /**
     * @test
     */
    public function throwsExceptionOnInvalidCallerResult()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Controller action result must be either ResponseInterface or string');

        $this->container->shouldReceive('call')
                        ->with($this->route->getCallable(), $this->route->getParameters())
                        ->andReturn(new stdClass)
                        ->once();
        $strategy = new \Venta\Routing\Strategy\Generic($this->container, $this->factory);
        $result = $strategy->dispatch($this->route);
    }

}
