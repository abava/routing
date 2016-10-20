<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Container\Container;
use Venta\Contracts\Routing\Delegate;
use Venta\Contracts\Routing\Route as RouteContract;

/**
 * Class RouteDispatcher
 *
 * @package Venta\Routing
 */
class RouteDispatcher implements Delegate
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