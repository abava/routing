<?php declare(strict_types = 1);

namespace Venta\Routing;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\Dispatcher;
use FastRoute\RouteParser\Std;
use Traversable;
use Venta\Contracts\Routing\Route;

/**
 * Class RouteCollectionParser
 *
 * @package Venta\Routing
 */
class RouteCollectionParser
{

    /**
     * @var GroupCountBased
     */
    protected $dataGenerator;

    /**
     * @var Std
     */
    protected $parser;

    /**
     * RouteCollectionParser constructor.
     *
     * @param Std $parser
     * @param GroupCountBased $dataGenerator
     */
    public function __construct(Std $parser, GroupCountBased $dataGenerator)
    {
        $this->parser = $parser;
        $this->dataGenerator = $dataGenerator;
    }

    /**
     * Returns parsed data
     *
     * @param Traversable $routeCollection
     * @return array
     */
    public function iterate(Traversable $routeCollection): array
    {
        foreach ($routeCollection as $route) {
            $parsedData = $this->parser->parse($route->getPath());
            foreach ($route->getMethods() as $method) {
                $this->dataGenerator->addRoute($method, $parsedData, $route);
            }
        }

        return $this->dataGenerator->getData();
    }

    /**
     * Parses route collection and creates dispatcher
     *
     * @param Traversable|Route[] $routeCollection
     * @return Dispatcher
     */
    public function parse(Traversable $routeCollection): Dispatcher
    {
        return new Dispatcher\GroupCountBased($this->iterate($routeCollection));
    }

}