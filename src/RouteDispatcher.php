<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Container\Container;
use Venta\Contracts\Container\ContainerAware;
use Venta\Contracts\Routing\Delegate;

/**
 * Class RouteDispatcher
 *
 * @package Venta\Routing
 */
class RouteDispatcher implements Delegate, ContainerAware
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Route
     */
    protected $route;

    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    /**
     * @inheritDoc
     */
    public function next(ServerRequestInterface $request): ResponseInterface
    {
        return $this->container->call($this->route->getHandler(), ['request' => $request]);
    }

    /**
     * @inheritDoc
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

}