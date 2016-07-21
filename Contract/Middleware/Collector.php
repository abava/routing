<?php declare(strict_types = 1);

namespace Abava\Routing\Contract\Middleware;

/**
 * Class Collector
 *
 * @package Abava\Routing\Contract\Middleware
 */
interface Collector extends \Iterator
{

    /**
     * Add middleware to the end
     *
     * @param string $name
     * @param $middleware
     * @return void
     */
    public function pushMiddleware(string $name, $middleware);

    /**
     * Add middleware after $after named middleware
     *
     * @param string $after
     * @param string $name
     * @param $middleware
     * @return void
     */
    public function pushAfter(string $after, string $name, $middleware);

    /**
     * Add middleware before $before named middleware
     *
     * @param string $before
     * @param string $name
     * @param $middleware
     * @return void
     */
    public function pushBefore(string $before, string $name, $middleware);

    /**
     * Check if $name middleware has already been added to the collector
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;

}