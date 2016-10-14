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

        // Creating deferred middleware
        $globalMiddlewarePipeline = new DeferredMiddlewarePipeline($globalMiddlewares);

        // Creating router delegate, which will match route, process route middleware and call route handler
        $delegate = $this->container->get(Router::class);

        // Running middleware to get response
        $response = $globalMiddlewarePipeline->process($request, $delegate);

        /** @var Emitter $emitter */
        $emitter = $this->container->get(Emitter::class);
        // Emitting response
        $emitter->emit($response);

    }

}