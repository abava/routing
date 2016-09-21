<?php declare(strict_types = 1);

namespace Venta\Routing\Contract;

/**
 * Interface UrlBuilder
 *
 * @package Venta\Routing\Contracts
 */
interface UrlBuilder
{

    /**
     * Build url using provided parameters on placeholders.
     *
     * @param array $parameters
     * @return string
     */
    public function url(array $parameters = []): string;

}