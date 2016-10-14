<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Routing\Delegate as DelegateContract;

/**
 * Class RouteMiddlewarePipeline
 *
 * @package Venta\Routing
 */
class RouteMiddlewarePipeline extends DeferredMiddlewarePipeline
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, DelegateContract $delegate): ResponseInterface
    {
        /** @var Route $route */
        $route = $request->getAttribute('route');
        $this->middlewares = $route->getMiddlewares();

        return parent::process($request, $delegate);
    }


}