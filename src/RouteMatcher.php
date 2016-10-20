<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Routing\DispatcherFactory as DispatcherFactoryContract;
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
class RouteMatcher implements RouteMatcherContract
{

    /**
     * @var DispatcherFactoryContract
     */
    protected $dispatcherFactory;

    /**
     * @var RouteParserContract
     */
    protected $parser;

    /**
     * RouteMatcher constructor.
     *
     * @param RouteParserContract $parser
     * @param DispatcherFactoryContract $dispatcherFactory
     */
    public function __construct(RouteParserContract $parser, DispatcherFactoryContract $dispatcherFactory)
    {
        $this->parser = $parser;
        $this->dispatcherFactory = $dispatcherFactory;
    }

    /**
     * @inheritDoc
     */
    public function match(ServerRequestInterface $request, RouteCollectionContract $routeCollection): RouteContract
    {
        $parsedRouteData = $this->parser->parse($routeCollection->getRoutes());
        $dispatcher = $this->dispatcherFactory->create($parsedRouteData);
        $match = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
        switch ($match[0]) {
            case $dispatcher::FOUND:
                /** @var Route $route */
                list(, $route, $variables) = $match;

                return $route;//->withVariables($variables); TODO: add ->withVariables to interface?
                break;
            case $dispatcher::METHOD_NOT_ALLOWED:
                throw new NotAllowedException($match[1]);
            default:
                throw new NotFoundException;
        }
    }

}