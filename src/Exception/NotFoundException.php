<?php declare(strict_types = 1);

namespace Venta\Routing\Exception;

/**
 * Class NotFoundException
 *
 * @package Venta\Routing\Exception
 */
class NotFoundException extends \LogicException
{
    /**
     * {@inheritdoc}
     */
    public function __construct(string $method, string $path, \Exception $previous = null)
    {
        parent::__construct(sprintf('Cannot route "%s %s" request.', $method, $path), 0, $previous);
    }
}