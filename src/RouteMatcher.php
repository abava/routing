<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Routing\FastrouteDispatcherFactory;
use Venta\Contracts\Routing\Route as RouteContract;
use Venta\Contracts\Routing\RouteCollection as RouteCollectionContract;
use Venta\Contracts\Routing\RouteMatcher as RouteMatcherContract;
use Venta\Contracts\Routing\RouteParser as RouteParserContract;
use Venta\Routing\Exception\NotAllowedException;
use Venta\Routing\Exception\NotFoundException;

/**
 * Class RouteMatcher
 *
 * @package Venta\Routing
 */
final class RouteMatcher implements RouteMatcherContract
{

    /**
     * @var FastrouteDispatcherFactory
     */
    private $fastrouteDispatcherFactory;

    /**
     * @var RouteParserContract
     */
    private $parser;

    /**
     * RouteMatcher constructor.
     *
     * @param RouteParserContract $parser
     * @param FastrouteDispatcherFactory $fastrouteDispatcherFactory
     */
    public function __construct(RouteParserContract $parser, FastrouteDispatcherFactory $fastrouteDispatcherFactory)
    {
        $this->parser = $parser;
        $this->fastrouteDispatcherFactory = $fastrouteDispatcherFactory;
    }

    /**
     * @inheritDoc
     */
    public function match(ServerRequestInterface $request, RouteCollectionContract $routeCollection): RouteContract
    {
        $routes = $routeCollection->getRoutes();
        $parsedRouteData = $this->parser->parse($routes);
        $dispatcher = $this->fastrouteDispatcherFactory->create($parsedRouteData);
        $match = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
        switch ($match[0]) {
            case $dispatcher::FOUND:
                /** @var RouteContract $route */
                list(, $route, $variables) = $match;

                return $route->withVariables($variables);
            case $dispatcher::METHOD_NOT_ALLOWED:
                throw new NotAllowedException($match[1]);
            default:
                throw new NotFoundException;
        }
    }

}