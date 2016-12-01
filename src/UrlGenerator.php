<?php


namespace Venta\Routing;


use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Venta\Contracts\Http\Request;
use Venta\Contracts\Routing\Route;
use Venta\Contracts\Routing\RouteCollection;
use Venta\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Venta\Routing\Exception\RouteNotFoundException;

class UrlGenerator implements UrlGeneratorContract
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var RouteCollection
     */
    private $routes;

    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * UrlGenerator constructor.
     *
     * @param Request $request
     * @param RouteCollection $routes
     * @param UriInterface $uri
     */
    public function __construct(Request $request, RouteCollection $routes, UriInterface $uri)
    {
        $this->request = $request;
        $this->routes = $routes;
        $this->uri = $uri;
    }

    /**
     * @inheritDoc
     */
    public function toCurrent(array $variables = [], array $query = []): UriInterface
    {
        $route = $this->request->getRoute();

        if ($route === null) {
            throw new RouteNotFoundException(
                sprintf('Unable to generate an URL for current.')
            );
        }

        return $this->buildRouteUri($route, $variables, $query);
    }

    /**
     * @inheritDoc
     */
    public function toRoute(string $routeName, array $variables = [], array $query = []): UriInterface
    {
        $route = $this->routes->findByName($routeName);

        if ($route === null) {
            throw new RouteNotFoundException(
                sprintf('Unable to generate an URL for the named route "%s" as such route does not exist.', $routeName)
            );
        }

        return $this->buildRouteUri($route, $variables, $query);
    }

    /**
     * Builds URI for provided route instance.
     *
     * @param Route $route
     * @param array $variables
     * @param array $query
     * @return UriInterface
     */
    private function buildRouteUri(Route $route, array $variables = [], array $query = []): UriInterface
    {
        $uri = $this->uri
            ->withScheme($route->getScheme() ?: $this->request->getUri()->getScheme())
            ->withHost($route->getHost() ?: $this->request->getUri()->getHost())
            ->withPath($route->compilePath($variables));

        if ($query) {
            $uri = $uri->withQuery(http_build_query($query));
        }

        return $uri;
    }
}