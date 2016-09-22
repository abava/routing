<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\RequestInterface;
use Venta\Contracts\Routing\DispatcherFactory;
use Venta\Contracts\Routing\RouteCollector as RouteCollectorContract;
use Venta\Contracts\Routing\RouteMatcher as RouteMatcherContract;
use Venta\Routing\Exceptions\NotAllowedException;
use Venta\Routing\Exceptions\NotFoundException;

/**
 * Class RouteMatcher
 *
 * @package Venta\Routing
 */
class RouteMatcher implements RouteMatcherContract
{

    /**
     * Dispatcher factory instance.
     *
     * @var DispatcherFactory
     */
    protected $factory;

    /**
     * RouteMatcher constructor.
     *
     * @param DispatcherFactory $factory
     */
    public function __construct(DispatcherFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc
     * @throws NotFoundException
     * @throws NotAllowedException
     */
    public function match(RequestInterface $request, RouteCollectorContract $collector): Route
    {
        $dispatcher = $this->factory->create($collector->getFilteredData($request));
        $match = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
        switch ($match[0]) {
            case $dispatcher::FOUND:
                /** @var Route $route */
                $route = $match[1];

                return $route->withParameters($match[2]);
                break;
            case $dispatcher::METHOD_NOT_ALLOWED:
                throw new NotAllowedException($match[1]);
            default:
                throw new NotFoundException;
        }
    }

}