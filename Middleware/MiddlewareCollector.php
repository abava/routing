<?php declare(strict_types = 1);

namespace Venta\Routing\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Venta\Contracts\Container\Container;
use Venta\Contracts\Routing\Middleware;
use Venta\Contracts\Routing\MiddlewareCollector as MiddlewareCollectorContract;

/**
 * Class MiddlewareCollector
 *
 * @package Venta\Routing\Middleware
 */
class MiddlewareCollector implements MiddlewareCollectorContract
{

    use MiddlewareValidatorTrait;

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
     * If $order is reversed
     *
     * @var bool
     */
    private $reversed = false;

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
     * @return \Venta\Contracts\Routing\Middleware
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
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return isset($this->middlewares[$name]);
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
     * Returns next middleware name
     *
     * @return string
     */
    public function next()
    {
        return next($this->order);
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
        } elseif ($this->reversed) {
            throw new \RuntimeException("Middleware stack is reversed inside foreach loop, pushAfter is restricted");
        } elseif ($this->isValidMiddleware($middleware)) {
            $this->middlewares[$name] = $middleware;
            $afterIndex = array_search($after, $this->order);
            // Adding middleware after provided name
            $this->order = array_merge(
                array_slice($this->order, 0, $afterIndex + 1),
                [$name],
                array_slice($this->order, $afterIndex + 1)
            );
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
        } elseif ($this->reversed) {
            throw new \RuntimeException("Middleware stack is reversed inside foreach loop, pushBefore is restricted");
        } elseif ($this->isValidMiddleware($middleware)) {
            $this->middlewares[$name] = $middleware;
            $beforeIndex = array_search($before, $this->order);
            // Adding middleware before provided name
            $this->order = array_merge(
                array_slice($this->order, 0, $beforeIndex),
                [$name],
                array_slice($this->order, $beforeIndex)
            );
        } else {
            throw new \InvalidArgumentException('Middleware must either implement Middleware contract or be callable');
        }
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
            if ($this->reversed) {
                array_unshift($this->order, $name);
            } else {
                $this->order[] = $name;
            }
        } else {
            throw new \InvalidArgumentException('Middleware must either implement Middleware contract or be callable');
        }
    }

    /**
     * Rewinds middleware array
     *
     * @return int
     */
    public function rewind()
    {
        if (!$this->reversed) {
            $this->reverse();
        }

        return reset($this->order);
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
     * Wraps callable (e.g. closure) with anonymous class that implements Middleware contract
     * Does not check if callable's typehinting fits Middleware contract's handle method.
     *
     * @param callable $callable
     * @return \Venta\Contracts\Routing\Middleware
     */
    protected function wrapCallableToContract(
        /** @noinspection PhpUnusedParameterInspection */
        callable $callable
    ): Middleware
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

    /**
     * Reversed order and toggles reverse flag
     *
     * @return void
     */
    private function reverse()
    {
        $this->order = array_reverse($this->order);
        $this->reversed ^= 1;
    }

}