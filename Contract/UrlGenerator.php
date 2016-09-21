<?php declare(strict_types = 1);

namespace Venta\Routing\Contract;

/**
 * Interface UrlGenerator
 *
 * @package Venta\Routing\Contracts
 */
interface UrlGenerator
{

    /**
     * Generate url to named route
     *
     * @param string $name
     * @param array $parameters
     * @return string
     */
    public function url(string $name, array $parameters = []): string;

}