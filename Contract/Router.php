<?php declare(strict_types = 1);

namespace Abava\Routing\Contract;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface RouterContract
 *
 * @package Abava\Routing\Contract
 */
interface Router
{
    /**
     * Dispatches request
     *
     * @param RequestInterface $request Request
     * @return ResponseInterface
     */
    public function dispatch(RequestInterface $request): ResponseInterface;

}