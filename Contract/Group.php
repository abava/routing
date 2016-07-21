<?php declare(strict_types = 1);

namespace Abava\Routing\Contract;

/**
 * Interface Group
 *
 * @package Abava\Routing\Contract
 */
interface Group extends Collector
{

    /**
     * Set prefix for whole route group
     * 
     * @param string $prefix
     * @return Group
     */
    public function setPrefix(string $prefix): Group;

    /**
     * Set host for whole route group
     * 
     * @param string $host
     * @return Group
     */
    public function setHost(string $host): Group;
    
    /**
     * Set scheme for whole route group
     * 
     * @param string $scheme
     * @return Group
     */
    public function setScheme(string $scheme): Group;

    /**
     * Collect routes assigned to the group
     *
     * @return void
     */
    public function collect();

}