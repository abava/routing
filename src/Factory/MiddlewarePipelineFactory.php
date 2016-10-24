<?php declare(strict_types = 1);

namespace Venta\Routing\Factory;

use Venta\Contracts\Container\Container;
use Venta\Contracts\Routing\MiddlewarePipeline as MiddlewarePipelineContract;
use Venta\Contracts\Routing\MiddlewarePipelineFactory as MiddlewarePipelineFactoryContract;
use Venta\Routing\MiddlewarePipeline;

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
    public function create(array $middlewares): MiddlewarePipelineContract
    {
        $pipeline = new MiddlewarePipeline();
        foreach ($middlewares as $middleware) {
            $pipeline = $pipeline->withMiddleware($this->container->get($middleware));
        }

        return $pipeline;
    }

}
