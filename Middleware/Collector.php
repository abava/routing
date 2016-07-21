<?php declare(strict_types = 1);

namespace Abava\Routing\Middleware;

use Abava\Container\Contract\Container;
use Abava\Routing\Contract\Middleware;
use Abava\Routing\Contract\Middleware\Collector as CollectorContract;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Collector
 *
 * @package Abava\Routing\Middleware
 */
class Collector implements CollectorContract
{

    use ValidatorTrait;

    /**
     * Container instance is used to make middlewares provided as string
     *
     * @var Container
     */
    protected $container;

    /**
     * Middleware array
     *
     * @var array
     */
    protected $middlewares = [];

    /**
     * Middleware pipeline building order
     *
     * @var string[]
     */
    protected $order = [];

    /**
     * Collector constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function pushMiddleware(string $name, $middleware)
    {
        if ($this->has($name)) {
            throw new \InvalidArgumentException("Middleware '$name' is already defined");
        } elseif ($this->isValidMiddleware($middleware)) {
            $this->middlewares[$name] = $middleware;
            // Adding middleware to the end of the list
            $this->order[] = $name;
        } else {
            throw new \InvalidArgumentException('Middleware must either implement Middleware contract or be callable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function pushAfter(string $after, string $name, $middleware)
    {
        if ($this->has($name)) {
            throw new \InvalidArgumentException("Middleware '$name' is already defined");
        } elseif (!$this->has($after)) {
            throw new \InvalidArgumentException("Middleware '$after' cannot be found");
        } elseif ($this->isValidMiddleware($middleware)) {
            $this->middlewares[$name] = $middleware;
            $afterIndex = array_search($after, $this->order);
            // Adding middleware after provided name
            $this->order = array_slice($this->order, 0, $afterIndex) + [$name] + array_slice($this->order, $afterIndex);
        } else {
            throw new \InvalidArgumentException('Middleware must either implement Middleware contract or be callable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function pushBefore(string $before, string $name, $middleware)
    {
        if ($this->has($name)) {
            throw new \InvalidArgumentException("Middleware '$name' is already defined");
        } elseif (!$this->has($before)) {
            throw new \InvalidArgumentException("Middleware '$before' cannot be found");
        } elseif ($this->isValidMiddleware($middleware)) {
            $this->middlewares[$name] = $middleware;
            $beforeIndex = array_search($before, $this->order);
            // Adding middleware before provided name
            $this->order = array_slice($this->order, 0, $beforeIndex-1) + [$name] + array_slice($this->order, $beforeIndex);
        } else {
            throw new \InvalidArgumentException('Middleware must either implement Middleware contract or be callable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return isset($this->middlewares[$name]);
    }

    /**
     * @return Middleware
     */
    public function current()
    {
        $name = current($this->order);
        $middleware = $this->middlewares[$name];
        // Middleware instantiation is deferred
        if (is_string($middleware)) {
            // Make middleware using container instance
            return $this->middlewares[$name] = $this->container->make($middleware);
        } elseif (is_callable($middleware)) {
            // Wrap callable middleware to return MiddlewareContract instance
            return $this->middlewares[$name] = $this->wrapCallableToContract($middleware);
        } else {
            // No additional action needed, already instance of MiddlewareContract
            return $middleware;
        }
    }

    /**
     * Returns next middleware name
     *
     * @return string
     */
    public function next()
    {
        return next($this->order);
    }

    /**
     * Returns current middleware name
     *
     * @return string
     */
    public function key()
    {
        return $this->order[key($this->order)];
    }

    /**
     * Returns falls in case middlewares are over
     *
     * @return bool
     */
    public function valid()
    {
        return key($this->order) !== null;
    }

    /**
     * Rewinds middleware array
     *
     * @return int
     */
    public function rewind()
    {
        return reset($this->order);
    }

    /**
     * Wraps callable (e.g. closure) with anonymous class that implements Middleware contract
     * Does not check if callable's typehinting fits Middleware contract's handle method.
     *
     * @param callable $callable
     * @return Middleware
     */
    protected function wrapCallableToContract(
        /** @noinspection PhpUnusedParameterInspection */
        callable $callable): Middleware
    {
        return new class($callable) implements Middleware
        {

            /** @var callable */
            protected $callable;

            public function __construct(callable $callable)
            {
                $this->callable = $callable;
            }

            /**
             * {@inheritdoc}
             */
            public function handle(RequestInterface $request, \Closure $next): ResponseInterface
            {
                return ($this->callable)($request, $next);
            }

        };
    }

}