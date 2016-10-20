<?php declare(strict_types = 1);

namespace Venta\Routing;

use Venta\Contracts\Container\Container;
use Venta\Contracts\Routing\MiddlewarePipeline;
use Venta\Contracts\Routing\MiddlewarePipelineFactory as MiddlewarePipelineFactoryContract;

/**
 * Class MiddlewarePipelineFactory
 *
 * @package Venta\Routing
 */
class MiddlewarePipelineFactory implements MiddlewarePipelineFactoryContract
{
    /**
     * @var Container
     */
    private $container;

    /**
     * MiddlewarePipelineFactory constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function create(array $middlewares): MiddlewarePipeline
    {
        $pipeline = new \Venta\Routing\MiddlewarePipeline();
        foreach ($middlewares as $middleware) {
            $pipeline = $pipeline->withMiddleware($this->container->get($middleware));
        }

        return $pipeline;
    }

}
