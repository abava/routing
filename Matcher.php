<?php declare(strict_types = 1);

namespace Abava\Routing;

use Abava\Routing\Contract\Collector as RouteCollector;
use Abava\Routing\Contract\Matcher as MatcherContract;
use Abava\Routing\Exceptions\NotAllowedException;
use Abava\Routing\Exceptions\NotFoundException;
use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispatcher;
use Psr\Http\Message\RequestInterface;

/**
 * Class Dispatcher
 *
 * @package Abava\Routing
 */
class Matcher implements MatcherContract
{

    /**
     * {@inheritdoc
     * @throws NotFoundException
     * @throws NotAllowedException
     */
    public function match(RequestInterface $request, RouteCollector $collector): Route
    {
        $dispatcher = new GroupCountBasedDispatcher($collector->getFilteredData($request));
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