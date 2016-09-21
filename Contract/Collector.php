<?php declare(strict_types = 1);

namespace Venta\Routing\Contract;

use Venta\Routing\Route;
use FastRoute\DataGenerator;
use Psr\Http\Message\RequestInterface;

/**
 * Interface Collector
 *
 * @package Venta\Routing\Contracts
 */
interface Collector extends DataGenerator
{

    /**
     * Add route to collector
     *
     * @param Route $route
     * @return void
     */
    public function add(Route $route);

    /**
     * Filters route collection to fit provided request
     *
     * @param RequestInterface $request
     * @return array
     */
    public function getFilteredData(RequestInterface $request): array;

    /**
     * Returns all collected routes
     *
     * @return Route[]
     */
    public function getRoutes(): array;

    /**
     * Add a group of routes
     *
     * @param string $prefix
     * @param callable $callback
     * @return Group
     */
    public function group(string $prefix, callable $callback): Group;

}