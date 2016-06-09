<?php declare(strict_types = 1);

namespace Venta\Routing;

use FastRoute\DataGenerator;
use FastRoute\RouteParser;

/**
 * Class RoutesCollector
 *
 * @package Venta\Routing
 */
class RoutesCollector
{
    /**
     * Routes parser holder
     *
     * @var RouteParser
     */
    protected $parser;

    /**
     * Routes data generator holder
     *
     * @var DataGenerator
     */
    protected $generator;

    /**
     * Construct function
     *
     * @param RouteParser   $parser
     * @param DataGenerator $generator
     */
    public function __construct(RouteParser $parser, DataGenerator $generator)
    {
        $this->parser = $parser;
        $this->generator = $generator;
    }

    /**
     * Register GET route
     *
     * @param string $route
     * @param mixed  $handle
     */
    public function get(string $route, $handle)
    {
        $this->addRoute(['GET', 'HEAD'], $route, $handle);
    }

    /**
     * Register POST route
     *
     * @param string $route
     * @param mixed  $handle
     */
    public function post(string $route, $handle)
    {
        $this->addRoute(['POST'], $route, $handle);
    }

    /**
     * Register PATCH route
     *
     * @param string $route
     * @param mixed  $handle
     */
    public function patch(string $route, $handle)
    {
        $this->addRoute(['PATCH'], $route, $handle);
    }

    /**
     * Register PUT route
     *
     * @param string $route
     * @param mixed  $handle
     */
    public function put(string $route, $handle)
    {
        $this->addRoute(['PUT'], $route, $handle);
    }

    /**
     * Register OPTIONS route
     *
     * @param string $route
     * @param mixed  $handle
     */
    public function options(string $route, $handle)
    {
        $this->addRoute(['OPTIONS'], $route, $handle);
    }

    /**
     * Register DELETE route
     *
     * @param string $route
     * @param mixed  $handle
     */
    public function delete(string $route, $handle)
    {
        $this->addRoute(['DELETE'], $route, $handle);
    }

    /**
     * Returns array with all routes for matching
     *
     * @return array
     */
    public function getRoutesCollection(): array
    {
        return $this->generator->getData();
    }

    /**
     * Add route to routes collection
     *
     * @param array $methods
     * @param string $route
     * @param mixed  $handle
     */
    protected function addRoute(array $methods, string $route, $handle)
    {
        $routesData = $this->parser->parse($route);

        foreach ($methods as $method) {
            foreach ($routesData as $data) {
                $this->generator->addRoute($method, $data, $handle);
            }
        }
    }
}