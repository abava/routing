<?php declare(strict_types=1);

namespace Venta\Routing\Contract;

use Venta\Http\Contract\{
    RequestContract, ResponseContract
};

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
     * @param $request RequestContract
     * @return ResponseContract
     */
    public function dispatch(RequestContract $request): ResponseContract;

}