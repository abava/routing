<?php declare(strict_types = 1);

namespace Abava\Routing\Contract;

use Abava\Routing\Route;
use FastRoute\DataGenerator;
use Psr\Http\Message\RequestInterface;

/**
 * Interface Collector
 *
 * @package Abava\Routing\Contract
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
     * Add a group of routes
     *
     * @param string $prefix
     * @param callable $callback
     * @return Group
     */
    public function group(string $prefix, callable $callback): Group;

    /**
     * Add a route that responds to GET HTTP method.
     *
     * @param string          $path
     * @param string|callable $handler
     *
     * @return Route
     */
    public function get(string $path, $handler): Route;

    /**
     * Add a route that responds to POST HTTP method.
     *
     * @param string          $path
     * @param string|callable $handler
     *
     * @return Route
     */
    public function post(string $path, $handler): Route;

    /**
     * Add a route that responds to PUT HTTP method.
     *
     * @param string          $path
     * @param string|callable $handler
     *
     * @return Route
     */
    public function put(string $path, $handler): Route;

    /**
     * Add a route that responds to PATCH HTTP method.
     *
     * @param string          $path
     * @param string|callable $handler
     *
     * @return Route
     */
    public function patch(string $path, $handler): Route;

    /**
     * Add a route that responds to DELETE HTTP method.
     *
     * @param string          $path
     * @param string|callable $handler
     *
     * @return Route
     */
    public function delete(string $path, $handler): Route;

    /**
     * Add a route that responds to HEAD HTTP method.
     *
     * @param string          $path
     * @param string|callable $handler
     *
     * @return Route
     */
    public function head(string $path, $handler): Route;

    /**
     * Add a route that responds to OPTIONS HTTP method.
     *
     * @param string          $path
     * @param string|callable $handler
     *
     * @return Route
     */
    public function options(string $path, $handler): Route;

}