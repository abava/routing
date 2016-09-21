<?php declare(strict_types = 1);

namespace Venta\Routing\Contract;

/**
 * Interface Group
 *
 * @package Venta\Routing\Contracts
 */
interface Group extends Collector
{

    /**
     * Collect routes assigned to the group
     *
     * @return void
     */
    public function collect();

    /**
     * Set host for whole route group
     *
     * @param string $host
     * @return Group
     */
    public function setHost(string $host): Group;

    /**
     * Set prefix for whole route group
     *
     * @param string $prefix
     * @return Group
     */
    public function setPrefix(string $prefix): Group;

    /**
     * Set scheme for whole route group
     *
     * @param string $scheme
     * @return Group
     */
    public function setScheme(string $scheme): Group;

}