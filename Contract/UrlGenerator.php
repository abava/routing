<?php declare(strict_types = 1);

namespace Abava\Routing\Contract;

/**
 * Interface UrlGenerator
 *
 * @package Abava\Routing\Contract
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