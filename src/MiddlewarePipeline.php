<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Routing\Delegate as DelegateContract;
use Venta\Contracts\Routing\Middleware;
use Venta\Contracts\Routing\MiddlewarePipeline as MiddlewarePipelineContract;

/**
 * Class MiddlewarePipeline
 *
 * @package Venta\Routing
 */
class MiddlewarePipeline implements MiddlewarePipelineContract
{

    /**
     * @var Middleware[]
     */
    protected $middlewares = [];

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, DelegateContract $delegate): ResponseInterface
    {
        foreach (array_reverse($this->middlewares) as $middleware) {
            $delegate = $this->createDelegate($middleware, $delegate);
        }

        return $delegate->next($request);
    }

    /**
     * @inheritDoc
     */
    public function withMiddleware(Middleware $middleware): MiddlewarePipelineContract
    {
        $pipeline = clone $this;
        $pipeline->middlewares[] = $middleware;

        return $pipeline;
    }

    /**
     * @param Middleware $middleware
     * @param DelegateContract $nextDelegate
     * @return DelegateContract
     */
    protected function createDelegate($middleware, DelegateContract $nextDelegate): DelegateContract
    {
        return new class($middleware, $nextDelegate) implements DelegateContract
        {

            /**
             * @var Middleware
             */
            private $middleware;

            /**
             * @var DelegateContract
             */
            private $delegate;

            /**
             *  Delegate constructor.
             *
             * @param Middleware $middleware
             * @param DelegateContract $nextDelegate
             */
            public function __construct(Middleware $middleware, DelegateContract $nextDelegate)
            {
                $this->middleware = $middleware;
                $this->delegate = $nextDelegate;
            }

            /**
             * @inheritDoc
             */
            public function next(ServerRequestInterface $request): ResponseInterface
            {
                return $this->middleware->process($request, $this->delegate);
            }
        };
    }

}