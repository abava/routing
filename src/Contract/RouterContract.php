<?php declare(strict_types=1);

namespace Abava\Routing\Contract;

use Abava\Http\Contract\{
    RequestContract, ResponseContract
};

/**
 * Interface RouterContract
 *
 * @package Abava\Routing\Contract
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