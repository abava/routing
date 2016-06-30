<?php declare(strict_types=1);

namespace Venta\Routing\Contract;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface RouterContract
 *
 * @package Venta\Routing\Contract
 */
interface RouterContract
{

    /**
     * Dispatches request
     *
     * @param $request RequestInterface
     * @return ResponseInterface
     */
    public function dispatch(RequestInterface $request): ResponseInterface;

}