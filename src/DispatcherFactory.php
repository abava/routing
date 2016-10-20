<?php declare(strict_types = 1);

namespace Venta\Routing;

use FastRoute\Dispatcher;
use Venta\Contracts\Routing\DispatcherFactory as DispatcherFactoryContract;

/**
 * Class DispatcherFactory
 *
 * @package Venta\Routing
 */
class DispatcherFactory implements DispatcherFactoryContract
{
    /**
     * @inheritDoc
     */
    public function create(array $data): Dispatcher
    {
        return new Dispatcher\GroupCountBased($data);
    }
}