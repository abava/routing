<?php declare(strict_types = 1);

namespace Venta\Routing\Factory;

use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Routing\RequestRouteCollectionFactory as RequestRouteCollectionFactoryContract;
use Venta\Contracts\Routing\RouteCollection;
use Venta\Routing\RequestRouteCollection;

/**
 * Class RequestRouteCollectionFactory
 *
 * @package Venta\Routing\Factory
 */
final class RequestRouteCollectionFactory implements RequestRouteCollectionFactoryContract
{

    /**
     * @inheritDoc
     */
    public function create(RouteCollection $routeCollection, ServerRequestInterface $request): RouteCollection
    {
        return new RequestRouteCollection($request, $routeCollection);
    }

}
