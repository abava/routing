<?php declare(strict_types = 1);

namespace Abava\Routing\Contract;

/**
 * Interface UrlBuilder
 *
 * @package Abava\Routing\Contract
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