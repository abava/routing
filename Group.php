<?php declare(strict_types = 1);

namespace Abava\Routing;

use Abava\Routing\Contract\Collector as RouteCollectorContract;
use Abava\Routing\Contract\Group as GroupRouteCollectorContract;
use Psr\Http\Message\RequestInterface;

/**
 * Class Group
 *
 * @package Abava\Routing
 */
class Group implements RouteCollectorContract, GroupRouteCollectorContract
{
    use CollectorTrait;

    /**
     * Route collector instance
     *
     * @var Collector
     */
    protected $collector;

    /**
     * Captured route array
     *
     * @var Route[]
     */
    protected $routes = [];

    /**
     * Prefix to prepend to each route
     *
     * @var string
     */
    protected $prefix;

    /**
     * Host to set to each route
     *
     * @var string
     */
    protected $host = '';

    /**
     * Scheme to set to each route
     *
     * @var string
     */
    protected $scheme = '';

    /**
     * Callback to collect routes
     *
     * @var callable
     */
    protected $callback;

    /**
     * Group constructor.
     *
     * @param $prefix
     * @param callable $callback
     * @param RouteCollectorContract $collector
     */
    public function __construct($prefix, callable $callback, RouteCollectorContract $collector)
    {
        $this->prefix = sprintf('/%s', trim($prefix, '/'));
        $this->collector = $collector;
        $this->callback = $callback;
    }

    /**
     * Add route directly to collector
     *
     * @param string $method
     * @param string $path
     * @param mixed $handler
     * @thrown InvalidArgumentException
     * @return void
     */
    public function addRoute($method, $path, $handler)
    {
        $this->routes[] = new Route((array) $method, $this->addPrefixToPath($path), $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function add(Route $route)
    {
        $this->routes[] = $route;
    }


    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        ($this->callback)($this);
        foreach ($this->routes as $route) {
            if ($this->host) {
                $route = $route->withHost($this->host);
            }
            if ($this->scheme) {
                $route = $route->withScheme($this->scheme);
            }
            $this->collector->add($route->withPath($this->addPrefixToPath($route->getPath())));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->collector->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function getFilteredData(RequestInterface $request): array
    {
        return $this->collector->getFilteredData($request);
    }

    /**
     * @inheritDoc
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * {@inheritdoc}
     */
    public function group(string $prefix, callable $callback): GroupRouteCollectorContract
    {
        $group = $this->collector->group($this->addPrefixToPath($prefix), $callback);
        $group->setHost($this->host);
        $group->setScheme($this->scheme);
        return $group;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrefix(string $prefix): GroupRouteCollectorContract
    {
        $this->prefix = $prefix;
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setHost(string $host): GroupRouteCollectorContract
    {
        $this->host = $host;
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setScheme(string $scheme): GroupRouteCollectorContract
    {
        $this->scheme = $scheme;
        
        return $this;
    }

    /**
     * Adds group prefix to provided path
     *
     * @param string $path
     * @return string
     */
    protected function addPrefixToPath(string $path): string
    {
        return $path === '/' || $path === '' ? $this->prefix : rtrim($this->prefix, '/') . '/' . ltrim($path, '/');
    }

}