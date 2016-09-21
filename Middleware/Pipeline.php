<?php declare(strict_types = 1);

namespace Venta\Routing\Middleware;

use Venta\Routing\Contract\Middleware;
use Venta\Routing\Contract\Middleware\Pipeline as PipelineContract;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Pipeline
 *
 * @package Venta\Routing\Middleware
 */
class Pipeline implements PipelineContract
{

    /**
     * Middleware collector instance
     *
     * @var Collector
     */
    protected $middlewares;

    /**
     * Middleware Pipeline constructor.
     *
     * @param Collector $collector
     */
    public function __construct(Collector $collector)
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
                /** @var Middleware $middleware */
                return $middleware->handle($request, $next);
            };
        }

        return $next($request);
    }

}