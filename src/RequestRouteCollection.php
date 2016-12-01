<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Routing\RouteCollection as RouteCollectionContract;

/**
 * Class RequestRouteCollection
 *
 * @package Venta\Routing
 */
final class RequestRouteCollection implements RouteCollectionContract
{

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var RouteCollectionContract
     */
    private $routes;

    /**
     * RequestRouteCollection constructor.
     *
     * @param ServerRequestInterface $request
     * @param RouteCollectionContract $routes
     */
    public function __construct(ServerRequestInterface $request, RouteCollectionContract $routes)
    {
        $this->request = $request;
        $this->routes = $routes;
    }

    /**
     * @inheritDoc
     */
    public function findByName(string $routeName)
    {
        return $this->routes->findByName($routeName);
    }

    /**
     * @inheritDoc
     */
    public function getRoutes(): array
    {
        $routes = [];
        foreach ($this->routes->getRoutes() as $route) {
            if ((!$route->getHost() || $route->getHost() === $this->request->getUri()->getHost())
                && (!$route->getScheme() || $route->getScheme() === $this->request->getUri()->getScheme())
            ) {
                $routes[] = $route;
            }
        }

        return $routes;
    }

}
