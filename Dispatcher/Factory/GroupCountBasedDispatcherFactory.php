<?php declare(strict_types = 1);

namespace Venta\Routing\Dispatcher\Factory;

use FastRoute\Dispatcher;
use Venta\Contracts\Routing\DispatcherFactory;

/**
 * Class GroupCountBasedFactory
 *
 * @package Venta\Routing\Dispatcher\Factory
 */
class GroupCountBasedDispatcherFactory implements DispatcherFactory
{

    /**
     * @inheritDoc
     */
    public function create(array $data): Dispatcher
    {
        return new Dispatcher\GroupCountBased($data);
    }

}