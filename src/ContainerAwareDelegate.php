<?php declare(strict_types = 1);

namespace Venta\Routing;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Container\Container;
use Venta\Contracts\Container\ContainerAware;
use Venta\Contracts\Routing\Delegate;
use Venta\Contracts\Routing\Middleware;

/**
 * Class ContainerAwareDelegate
 *
 * @package Venta\Routing
 */
class ContainerAwareDelegate implements Delegate, ContainerAware
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Delegate
     */
    protected $delegate;

    /**
     * @var string
     */
    protected $middleware;

    /**
     * ContainerAwareDelegate constructor.
     *
     * @param string $middleware
     * @param Delegate $nextDelegate
     * @throws InvalidArgumentException
     */
    public function __construct(string $middleware, Delegate $nextDelegate)
    {
        if (!is_subclass_of($middleware, Middleware::class)) {
            throw new InvalidArgumentException(
                sprintf('Provided middleware "%s" does not implement %s interface', $middleware, Middleware::class)
            );
        }
        $this->middleware = $middleware;
        $this->delegate = $nextDelegate;
    }

    /**
     * @inheritDoc
     */
    public function next(ServerRequestInterface $request): ResponseInterface
    {
        /** @var Middleware $middleware */
        $middleware = $this->container->get($this->middleware);

        return $middleware->process($request, $this->delegate);
    }

    /**
     * @inheritDoc
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

}