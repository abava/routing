<?php declare(strict_types = 1);

namespace Abava\Routing\Contract;

use Abava\Http\Contract\{
    Request, Response
};

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
     * @param $request Request
     * @return Response
     */
    public function dispatch(Request $request): Response;

}