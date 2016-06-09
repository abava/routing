<?php declare(strict_types = 1);

namespace Venta\Routing;

use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteParser\Std;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Venta\Routing\Contract\MiddlewareContract;
use Venta\Routing\Exceptions\NotAllowedException;
use Venta\Routing\Exceptions\NotFoundException;

/**
 * Class Router
 *
 * @package Venta\Routing
 */
class Router
{
    /**
     * Container instance holder
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Dispatcher instance holder
     *
     * @var GroupCountBased
     */
    protected $dispatcher;

    /**
     * Flag, indicating if routes where collected
     *
     * @var bool
     */
    protected $routesCollected = false;

    /**
     * Array of defined middleware
     *
     * @var array
     */
    protected $middleware;

    /**
     * Construct function
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->middleware = [];
    }

    /**
     * Collect routes with passed in collector callable
     *
     * @param  callable $collectionCallback
     * @return $this
     */
    public function collectRoutes(callable $collectionCallback)
    {
        if ($this->routesCollected === false) {
            $collector = new RoutesCollector(new Std, new \FastRoute\DataGenerator\GroupCountBased);
            $collectionCallback($collector);

            $this->dispatcher = new GroupCountBased($collector->getRoutesCollection());
            $this->routesCollected = true;
        }

        return $this;
    }

    /**
     * Dispatch router
     *
     * @param string $method
     * @param string $uri
     */
    public function dispatch(string $method, string $uri)
    {
        $match = $this->dispatcher->dispatch($method, $uri);

        switch ($match[0]) {
            case GroupCountBased::FOUND:
                $pipe = $this->buildMiddlewarePipeline($match[1], $match[2]);
                $pipe($this->container->get(RequestInterface::class));
                break;
            case GroupCountBased::METHOD_NOT_ALLOWED:
                throw new NotAllowedException($match[1]);
            default:
                throw new NotFoundException;
        }
    }

    /**
     * Define middleware to use
     *
     * @param string             $name
     * @param MiddlewareContract $middleware
     */
    public function addMiddleware(string $name, MiddlewareContract $middleware)
    {
        $this->middleware[$name] = $middleware;
    }

    /**
     * Handles found route
     *
     * @param  mixed $handler
     * @param  array $parameters
     * @return ResponseInterface
     */
    protected function handleFoundRoute($handler, array $parameters)
    {
        $controller = $this->container->call($handler, $parameters);
        $response = $this->container->get('response');

        if (is_string($controller)) {
            $response->getBody()->write($controller);
        }

        return $response;
    }

    /**
     * Build pipeline to be executed before route handler
     *
     * @param  mixed $handler
     * @param  array $parameters
     * @return \Closure
     */
    protected function buildMiddlewarePipeline($handler, array $parameters): \Closure
    {
        $next = $this->getLastStep($handler, $parameters);
        $middleware = array_reverse($this->middleware);

        foreach ($middleware as $class) {
            $next = function (RequestInterface $request) use ($class, $next) {
                /** @var MiddlewareContract $class */
                return $class->handle($request, $next);
            };
        }

        return $next;
    }

    /**
     * Returns middleware pipeline last step
     *
     * @param  mixed $handler
     * @param  array $parameters
     * @return \Closure
     */
    protected function getLastStep($handler, array $parameters): \Closure
    {
        return function () use ($handler, $parameters) {
            return $this->handleFoundRoute($handler, $parameters);
        };
    }
}