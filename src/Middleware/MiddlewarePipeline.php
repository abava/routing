<?php declare(strict_types = 1);

namespace Venta\Routing\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Venta\Contracts\Routing\Middleware;
use Venta\Contracts\Routing\MiddlewarePipeline as MiddlewarePipelineContract;

/**
 * Class MiddlewarePipeline
 *
 * @package Venta\Routing\Middleware
 */
class MiddlewarePipeline implements MiddlewarePipelineContract
{

    /**
     * Middleware collector instance
     *
     * @var MiddlewareCollector
     */
    protected $middlewares;

    /**
     * Middleware Pipeline constructor.
     *
     * @param MiddlewareCollector $collector
     */
    public function __construct(MiddlewareCollector $collector)
    {
        $this->middlewares = $collector;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(RequestInterface $request, callable $last): ResponseInterface
    {
        $next = $last;

        foreach ($this->middlewares as $middleware) {
            $next = function (RequestInterface $request) use ($middleware, $next) {
                /** @var \Venta\Contracts\Routing\Middleware $middleware */
                return $middleware->handle($request, $next);
            };
        }

        return $next($request);
    }

}