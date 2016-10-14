<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Venta\Routing\Exception\NotAllowedException;
use Venta\Routing\Exception\NotFoundException;

/**
 * Class RouteMatcher
 *
 * @package Venta\Routing
 */
class RouteMatcher
{

    /**
     * @var RouteCollection
     */
    protected $collection;

    /**
     * @var RouteCollectionParser
     */
    protected $parser;

    /**
     * RouteMatcher constructor.
     *
     * @param RouteCollectionParser $parser
     */
    public function __construct(RouteCollectionParser $parser, RouteCollection $routeCollection)
    {
        $this->parser = $parser;
        $this->collection = $routeCollection;
    }

    /**
     * @param ServerRequestInterface $request
     * @return Route
     * @throws NotAllowedException
     * @throws NotFoundException
     * @internal param RouteCollection $collection
     */
    public function match(ServerRequestInterface $request)
    {
        $dispatcher = $this->parser->parse($this->collection);
        $match = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
        switch ($match[0]) {
            case $dispatcher::FOUND:
                /** @var Route $route */
                list(, $route, $variables) = $match;

                return $route->withVariables($variables);
                break;
            case $dispatcher::METHOD_NOT_ALLOWED:
                throw new NotAllowedException($match[1]);
            default:
                throw new NotFoundException;
        }
    }

}