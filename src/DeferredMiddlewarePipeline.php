<?php declare(strict_types = 1);

namespace Venta\Routing;

use Venta\Contracts\Container\Container;
use Venta\Contracts\Container\ContainerAware;
use Venta\Contracts\Routing\Delegate as DelegateContract;

/**
 * Class DeferredMiddlewarePipeline
 *
 * @package Venta\Routing
 */
class DeferredMiddlewarePipeline extends MiddlewarePipeline implements ContainerAware
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * DeferredMiddlewarePipeline constructor.
     *
     * @param array $middlewareList
     */
    public function __construct(array $middlewareList = [])
    {
        $this->middlewares = $middlewareList;
    }

    /**
     * @inheritDoc
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param array $middlewareList
     * @return MiddlewarePipeline
     */
    public function withMiddlewareList(array $middlewareList): MiddlewarePipeline
    {
        $pipeline = clone $this;
        $pipeline->middlewares = $middlewareList;

        return $pipeline;
    }

    /**
     * @inheritDoc
     * @param string $middleware
     */
    protected function createDelegate($middleware, DelegateContract $nextDelegate): DelegateContract
    {
        $delegate = new ContainerAwareDelegate($middleware, $nextDelegate);
        $delegate->setContainer($this->container);

        return $delegate;
    }


}