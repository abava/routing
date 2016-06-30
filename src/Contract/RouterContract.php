<?php declare(strict_types=1);

namespace Venta\Routing\Contract;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface RouterContract
 *
 * @package Venta\Routing\Contract
 */
interface RouterContract
{

    /**
     * Dispatches
     *
     * @param string $method
     * @param string $uri
     * @return ResponseInterface
     */
    public function dispatch(string $method, string $uri): ResponseInterface;

}