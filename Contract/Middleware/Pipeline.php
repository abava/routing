<?php declare(strict_types = 1);

namespace Venta\Routing\Contract\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface Pipeline
 *
 * @package Venta\Routing\Contracts\Middleware
 */
interface Pipeline
{

    /**
     * Process middleware pipeline:
     * - add $last as the last middleware
     * - pass $request instance through pipeline
     * - expect Response instance on the end
     *
     * @param RequestInterface $request
     * @param callable $last
     * @return ResponseInterface
     */
    public function handle(RequestInterface $request, callable $last): ResponseInterface;

}