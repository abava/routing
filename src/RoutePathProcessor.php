<?php declare(strict_types = 1);

namespace Venta\Routing;

use Venta\Contracts\Routing\Route as RouteContract;
use Venta\Contracts\Routing\RoutePathProcessor as RoutePathProcessorContract;

/**
 * Class RoutePathProcessor
 *
 * @package Venta\Routing
 */
final class RoutePathProcessor implements RoutePathProcessorContract
{

    /**
     * Patterns to apply to route path: [pattern => replacement].
     *
     * @var string[]
     */
    private $patterns = [];

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
        return $route->withPath(preg_replace(
            array_keys($this->patterns),
            array_values($this->patterns),
            $this->processOptionalPlaceholders($route->path())
        ));
    }

    /**
     * Replaces {?placeholder} with [{placeholder}] FastRoute syntax.
     * Respects following segments by adding ] to the end.
     *
     * @param string $path
     * @return string
     */
    private function processOptionalPlaceholders(string $path): string
    {
        return preg_replace('/{\?(.+?)}/', '[{$1}', $path, -1, $count) . str_repeat(']', $count);
    }

}