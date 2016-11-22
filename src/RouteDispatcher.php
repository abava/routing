<?php declare(strict_types = 1);

namespace Venta\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Adr\Input;
use Venta\Contracts\Adr\Payload;
use Venta\Contracts\Adr\Responder;
use Venta\Contracts\Container\Container;
use Venta\Contracts\Http\Request as RequestContract;
use Venta\Contracts\Routing\Route as RouteContract;
use Venta\Contracts\Routing\RouteDispatcher as RouteDispatcherContract;
use Venta\Http\Request;

/**
 * Class RouteDispatcher
 *
 * @package Venta\Routing
 */
final class RouteDispatcher implements RouteDispatcherContract
{

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RouteContract
     */
    private $route;

    /**
     * RouteDispatcher constructor.
     *
     * @param RouteContract $route
     * @param Container $container
     */
    public function __construct(RouteContract $route, Container $container)
    {
        $this->route = $route;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function next(ServerRequestInterface $request): ResponseInterface
    {
        if (!$request instanceof RequestContract) {
            // Decorate PSR-7 ServerRequest.
            $request = new Request($request);
        }

        if ($this->container->isCallable($this->route->getDomain())) {
            if ($this->container->has($this->route->getInput())) {
                /** @var Input $input */
                $input = $this->container->get($this->route->getInput());
                $arguments = $input->process($request);
            }
            /** @var Payload $payload */
            $payload = $this->container->call($this->route->getDomain(), $arguments ?? []);
        }
        /** @var Responder $responder */
        $responder = $this->container->get($this->route->getResponder());
        
        return $responder->run($request, $payload ?? null);
    }

}