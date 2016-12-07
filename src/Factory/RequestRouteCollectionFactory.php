<?php declare(strict_types = 1);

namespace Venta\Routing\Factory;

use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Routing\ImmutableRouteCollection;
use Venta\Contracts\Routing\RequestRouteCollectionFactory as RequestRouteCollectionFactoryContract;
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
    public function create(
        ImmutableRouteCollection $routeCollection,
        ServerRequestInterface $request
    ): ImmutableRouteCollection
    {
        return new RequestRouteCollection($request, $routeCollection);
    }

}
