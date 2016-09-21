<?php declare(strict_types = 1);

namespace Venta\Routing;

use Venta\Routing\Contract\Collector as RouteCollector;
use Venta\Routing\Contract\Dispatcher\DispatcherFactory;
use Venta\Routing\Contract\Matcher as MatcherContract;
use Venta\Routing\Exceptions\NotAllowedException;
use Venta\Routing\Exceptions\NotFoundException;
use Psr\Http\Message\RequestInterface;

/**
 * Class Dispatcher
 *
 * @package Venta\Routing
 */
class Matcher implements MatcherContract
{

    /**
     * Dispatcher factory instance
     *
     * @var DispatcherFactory
     */
    protected $factory;

    public function __construct(DispatcherFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc
     * @throws NotFoundException
     * @throws NotAllowedException
     */
    public function match(RequestInterface $request, RouteCollector $collector): Route
    {
        $dispatcher = $this->factory->create($collector->getFilteredData($request));
        $match = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
        switch ($match[0]) {
            case $dispatcher::FOUND:
                /** @var Route $route */
                $route = $match[1];

                return $route->withParameters($match[2]);
                break;
            case $dispatcher::METHOD_NOT_ALLOWED:
                throw new NotAllowedException($match[1]);
            default:
                throw new NotFoundException;
        }
    }

}