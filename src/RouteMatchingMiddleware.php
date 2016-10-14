<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Routing\Delegate;
use Venta\Contracts\Routing\Middleware;
use Venta\Routing\Exception\NotAllowedException;
use Venta\Routing\Exception\NotFoundException;

/**
 * Class RouteMatchingMiddleware
 *
 * @package Venta\Routing
 */
class RouteMatchingMiddleware implements Middleware
{

    /**
     * @var RouteCollectionParser
     */
    protected $parser;

    /**
     * @var RouteCollection
     */
    protected $routeCollection;

    public function __construct(RouteCollection $routeCollection, RouteCollectionParser $parser)
    {
        $this->routeCollection = $routeCollection;
        $this->parser = $parser;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, Delegate $delegate): ResponseInterface
    {
        $dispatcher = $this->parser->parse($this->routeCollection);
        $match = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
        switch ($match[0]) {
            case $dispatcher::FOUND:
                /** @var Route $route */
                list(, $route, $variables) = $match;

                return $delegate->next($request->withAttribute('route', $route->withVariables($variables)));
                break;
            case $dispatcher::METHOD_NOT_ALLOWED:
                throw new NotAllowedException($match[1]);
            default:
                throw new NotFoundException;
        }
    }


}