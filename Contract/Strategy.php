<?php declare(strict_types = 1);

namespace Abava\Routing\Contract;

use Abava\Routing\Route;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface Strategy
 *
 * @package Abava\Routing\Contract
 */
interface Strategy
{

    /**
     * Dispatches provided route:
     * - Makes controller and calls action
     * - Invokes callable
     * - Handles returned value
     * - Creates new Response instance for result value, if needed
     *
     * @param Route $route
     * @return ResponseInterface
     */
    public function dispatch(Route $route): ResponseInterface;

}