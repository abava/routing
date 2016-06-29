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
     * @var ContainerInterface|\Venta\Container\Container
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
     * @var MiddlewareCollector
     */
    protected $middleware;

    /**
     * Construct function
     *
     * @param ContainerInterface $container
     * @param  callable $collectionCallback
     */
    public function __construct(ContainerInterface $container, callable $collectionCallback)
    {
        $this->container = $container;
        $this->middleware = $container->get(MiddlewareCollector::class);

        $this->collectRoutes($collectionCallback);
    }

    /**
     * Collect middlewares with passed in collector callable
     *
     * @param callable $collectionCallback
     * @return $this
     */
    public function collectMiddlewares(callable $collectionCallback)
    {
        $collectionCallback($this->middleware);
        return $this;
    }

    /**
     * Dispatch router
     *
     * @param string $method
     * @param string $uri
     * @return ResponseInterface
     */
    public function dispatch(string $method, string $uri): ResponseInterface
    {
        $match = $this->dispatcher->dispatch($method, $uri);

        switch ($match[0]) {
            case GroupCountBased::FOUND:
                $pipe = $this->buildMiddlewarePipeline($match[1], $match[2]);
                return $pipe($this->container->get(RequestInterface::class));
                break;
            case GroupCountBased::METHOD_NOT_ALLOWED:
                throw new NotAllowedException($match[1]);
            default:
                throw new NotFoundException;
        }
    }

    /**
     * Collect routes with passed in collector callable
     *
     * @param  callable $collectionCallback
     * @return $this
     */
    protected function collectRoutes(callable $collectionCallback)
    {
        $collector = new RoutesCollector(new Std, new \FastRoute\DataGenerator\GroupCountBased);
        $collectionCallback($collector);

        $this->dispatcher = new GroupCountBased($collector->getRoutesCollection());

        return $this;
    }

    /**
     * Handles found route
     *
     * @param  mixed $handler
     * @param  array $parameters
     * @return ResponseInterface
     * @throws \RuntimeException
     */
    protected function handleFoundRoute($handler, array $parameters): ResponseInterface
    {
        $controller = $this->container->call($handler, $parameters);

        if ($controller instanceof ResponseInterface) {
            // Response should be returned directly
            return $controller;
        }

        if (is_object($controller) && method_exists($controller, '__toString')) {
            // Try to get string out of object as last fallback
            $controller = $controller->__toString();
        }

        if (is_string($controller)) {
            // String supposed to be appended to response body
            /** @var ResponseInterface $response */
            $response = $this->container->get(ResponseInterface::class);
            $response->getBody()->write($controller);
            return $response;
        }

        throw new \RuntimeException('Controller action result must be either ResponseInterface or string');
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

        foreach ($this->middleware->getMiddlewares() as $class) {
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