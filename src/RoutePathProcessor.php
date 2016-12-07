<?php declare(strict_types = 1);

namespace Venta\Routing;

use Venta\Contracts\Routing\Route as RouteContract;
use Venta\Contracts\Routing\RoutePathProcessor as RoutePathProcessorContract;

/**
 * Class RoutePathProcessor
 *
 * @package Venta\Routing
 */
class RoutePathProcessor implements RoutePathProcessorContract
{

    /**
     * @var string[]
     */
    private $patterns = ['/{\?(.+?)}/' => '[{$1}]'];

    /**
     * @inheritDoc
     */
    public function addPattern(string $placeholder, string $regex): RoutePathProcessorContract
    {
        $this->patterns["/{{$placeholder}}/"] = "{{$placeholder}:{$regex}}";

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function process(RouteContract $route): RouteContract
    {
        return $route->withPath(
            preg_replace(array_keys($this->patterns), array_values($this->patterns), $route->getPath())
        );
    }


}