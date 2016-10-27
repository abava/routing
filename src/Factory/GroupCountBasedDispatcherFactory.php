<?php declare(strict_types = 1);

namespace Venta\Routing\Factory;

use FastRoute\Dispatcher;
use Venta\Contracts\Routing\FastrouteDispatcherFactory;

/**
 * Class GroupCountBasedDispatcherFactory
 *
 * @package Venta\Routing\Factory
 */
final class GroupCountBasedDispatcherFactory implements FastrouteDispatcherFactory
{
    /**
     * @inheritDoc
     */
    public function create(array $data): Dispatcher
    {
        return new Dispatcher\GroupCountBased($data);
    }
}