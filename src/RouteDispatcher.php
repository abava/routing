<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Container\Container;
use Venta\Contracts\Routing\Route as RouteContract;
use Venta\Contracts\Routing\RouteDispatcher as RouteDispatcherContract;

/**
 * Class RouteDispatcher
 *
 * @package Venta\Routing
 */
class RouteDispatcher implements RouteDispatcherContract
{

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RouteContract
     */
    private $route;

    /**
     * RouteDispatcher constructor.
     *
     * @param RouteContract $route
     * @param Container $container
     */
    public function __construct(RouteContract $route, Container $container)
    {
        $this->route = $route;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function next(ServerRequestInterface $request): ResponseInterface
    {
        return $this->container->call($this->route->getHandler(), ['request' => $request]);
    }

}