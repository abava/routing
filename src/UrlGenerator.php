<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Venta\Contracts\Routing\ImmutableRouteCollection as RouteCollectionContract;
use Venta\Contracts\Routing\Route as RouteContract;
use Venta\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Venta\Routing\Exception\RouteNotFoundException;

/**
 * Class UrlGenerator
 *
 * @package Venta\Routing
 */
class UrlGenerator implements UrlGeneratorContract
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
     * @var UriInterface
     */
    private $uri;

    /**
     * UrlGenerator constructor.
     *
     * @param ServerRequestInterface $request
     * @param RouteCollectionContract $routes
     * @param UriInterface $uri
     */
    public function __construct(ServerRequestInterface $request, RouteCollectionContract $routes, UriInterface $uri)
    {
        $this->request = $request;
        $this->routes = $routes;
        $this->uri = $uri;
    }

    /**
     * @inheritDoc
     */
    public function toAsset(string $asset): UriInterface
    {
        $uri = $this->uri
            ->withScheme($this->request->getUri()->getScheme())
            ->withHost($this->request->getUri()->getHost())
            ->withPath($asset);

        return $this->addPortToUri($uri);
    }

    /**
     * @inheritDoc
     */
    public function toCurrent(array $variables = [], array $query = []): UriInterface
    {
        $route = $this->request->getAttribute('route');

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
     * Adds port to URI if it is needed.
     *
     * @param UriInterface $uri
     * @return UriInterface
     */
    private function addPortToUri(UriInterface $uri): UriInterface
    {
        $requestPort = $this->request->getUri()->getPort();

        if (!in_array($requestPort, [80, 443])) {
            $uri = $uri->withPort($requestPort);
        }

        return $uri;
    }

    /**
     * Builds URI for provided route instance.
     *
     * @param RouteContract $route
     * @param array $variables
     * @param array $query
     * @return UriInterface
     */
    private function buildRouteUri(RouteContract $route, array $variables = [], array $query = []): UriInterface
    {
        $uri = $this->uri
            ->withScheme($route->scheme() ?: $this->request->getUri()->getScheme())
            ->withHost($route->host() ?: $this->request->getUri()->getHost())
            ->withPath($route->compilePath($variables));

        if ($query) {
            $uri = $uri->withQuery(http_build_query($query));
        }

        return $this->addPortToUri($uri);
    }
}