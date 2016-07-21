<?php declare(strict_types = 1);

namespace Abava\Routing;

/**
 * Trait CollectorTrait
 *
 * @package Abava\Routing
 */
trait CollectorTrait
{

    /**
     * Add a route that responds to GET HTTP method.
     *
     * @param string          $path
     * @param string|callable $handler
     *
     * @return Route
     */
    public function get(string $path, $handler): Route
    {
        return new Route(['GET'], $path, $handler);
    }

    /**
     * Add a route that responds to POST HTTP method.
     *
     * @param string          $path
     * @param string|callable $handler
     *
     * @return Route
     */
    public function post(string $path, $handler): Route
    {
        return new Route(['POST'], $path, $handler);
    }

    /**
     * Add a route that responds to PUT HTTP method.
     *
     * @param string          $path
     * @param string|callable $handler
     *
     * @return Route
     */
    public function put(string $path, $handler): Route
    {
        return new Route(['PUT'], $path, $handler);
    }

    /**
     * Add a route that responds to PATCH HTTP method.
     *
     * @param string          $path
     * @param string|callable $handler
     *
     * @return Route
     */
    public function patch(string $path, $handler): Route
    {
        return new Route(['PATCH'], $path, $handler);
    }

    /**
     * Add a route that responds to DELETE HTTP method.
     *
     * @param string          $path
     * @param string|callable $handler
     *
     * @return Route
     */
    public function delete(string $path, $handler): Route
    {
        return new Route(['DELETE'], $path, $handler);
    }

    /**
     * Add a route that responds to HEAD HTTP method.
     *
     * @param string          $path
     * @param string|callable $handler
     *
     * @return Route
     */
    public function head(string $path, $handler): Route
    {
        return new Route(['HEAD'], $path, $handler);
    }

    /**
     * Add a route that responds to OPTIONS HTTP method.
     *
     * @param string          $path
     * @param string|callable $handler
     *
     * @return Route
     */
    public function options(string $path, $handler): Route
    {
        return new Route(['OPTIONS'], $path, $handler);
    }

}