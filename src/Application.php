<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Container\Container;
use Venta\Contracts\Kernel\Kernel;

/**
 * Class Application
 *
 * @package Venta\Routing
 */
class Application
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * HttpApplication constructor.
     *
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $kernel->boot();
        $this->container = $kernel->getContainer();
    }

    /**
     * @inheritDoc
     */
    public function run(ServerRequestInterface $request)
    {
        // Retrieving global midleware list from config
        $globalMiddlewares = $this->container->get('config')->middlewares->toArray();

        // Appending route matching middleware, which adds 'route' attribute to $request
        $globalMiddlewares[] = RouteMatchingMiddleware::class;

        // Appending route middleware pipeline, which processes $route middleware list
        $globalMiddlewares[] = RouteMiddlewarePipeline::class;

        // Creating deferred middleware
        $globalMiddlewarePipeline = new DeferredMiddlewarePipeline($globalMiddlewares);

        // Creating route dispatcher, which will call route handler
        $delegate = $this->container->get(RouteDispatcher::class);

        // Running middleware to get response
        $response = $globalMiddlewarePipeline->process($request, $delegate);

        /** @var Emitter $emitter */
        $emitter = $this->container->get(Emitter::class);
        // Emitting response
        $emitter->emit($response);

    }

}