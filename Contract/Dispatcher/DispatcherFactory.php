<?php declare(strict_types = 1);

namespace Venta\Routing\Contract\Dispatcher;

use FastRoute\Dispatcher;

/**
 * Interface Factory
 *
 * @package Venta\Routing\Contracts\Dispatcher
 */
interface DispatcherFactory
{

    /**
     * Make dispatcher instance and pass $data array
     *
     * @param array $data
     * @return Dispatcher
     */
    public function create(array $data): Dispatcher;

}