<?php declare(strict_types = 1);

namespace Venta\Routing\Collection;

/**
 * Class Handlers
 *
 * @package Venta\Routing
 */
class Handlers
{
    /**
     * Defines, if handler is valid for usage
     *
     * @param  mixed $handler
     * @return bool
     */
    public function isValid($handler): bool
    {
        return $this->isClassWithMethod($handler);
    }

    /**
     * Defines, if handler is a class&method string
     *
     * @param  mixed $handler
     * @return bool
     */
    protected function isClassWithMethod($handler): bool
    {
        return is_string($handler) && strpos($handler, '@') !== false && substr_count($handler, '@') === 1;
    }
}