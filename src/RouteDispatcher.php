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
     * @inheritDoc
     */
    public function next(ServerRequestInterface $request): ResponseInterface
    {
        /** @var Route $route */
        $route = $request->getAttribute('route');

        return $this->container->call($route->getHandler(), ['request' => $request]);
    }

    /**
     * @inheritDoc
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

}