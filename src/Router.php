<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Routing\Delegate;
use Venta\Contracts\Routing\RouteCollection;

/**
 * Class Router
 *
 * @package Venta\Routing
 */
class Router implements Delegate
{

    private $collection;

    public function __construct(RouteCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @inheritDoc
     */
    public function next(ServerRequestInterface $request): ResponseInterface
    {
        $matcher = new Matcher();
        $route = $matcher->match($request, $this->collection);
        $pipeline = new MiddlewarePipeline();
        foreach ($route->getMiddleware() as $middleware) {
            $pipeline->withMiddleware($middleware);
        }

        return $pipeline->process($request, new RouteDispatcher($route));
    }


}