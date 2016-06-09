<?php declare(strict_types = 1);

namespace Venta\Routing\Exceptions;

/**
 * Class NotAllowedException
 *
 * @package Venta\Routing
 */
class NotAllowedException extends \LogicException
{
    /**
     * {@inheritdoc}
     *
     * @param array $allowedMethods
     */
    public function __construct(array $allowedMethods, \Exception $previous = null)
    {
        parent::__construct($this->buildMessage($allowedMethods), 0, $previous);
    }

    /**
     * Builds error message
     *
     * @param  array $allowedMethods
     * @return string
     */
    protected function buildMessage(array $allowedMethods): string
    {
        $ending = 'Allowed method is: %s';

        if (count($allowedMethods) > 1) {
            $ending = 'Allowed methods are: %s';
        }

        return sprintf('Method is not allowed. ' . $ending, implode(', ', $allowedMethods));
    }
}