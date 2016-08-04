<?php declare(strict_types = 1);

namespace Abava\Routing\Strategy;

use Abava\Container\Contract\Caller;
use Abava\Http\Factory\ResponseFactory;
use Abava\Routing\Contract\Strategy;
use Abava\Routing\Route;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Generic
 *
 * @package Abava\Routing\Strategy
 */
class Generic implements Strategy
{

    /**
     * Caller instance to create controller and call action
     *
     * @var Caller
     */
    protected $caller;

    /**
     * Response factory will create new response instance if controller action will not return any
     *
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * Generic strategy constructor.
     *
     * @param Caller $caller
     * @param ResponseFactory $responseFactory
     */
    public function __construct(Caller $caller, ResponseFactory $responseFactory)
    {
        $this->caller = $caller;
        $this->responseFactory = $responseFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(Route $route): ResponseInterface
    {
        $response = $this->caller->call($route->getCallable(), $route->getParameters());

        return $this->handleResult($response);
    }

    /**
     * @param $result
     * @return ResponseInterface
     */
    protected function handleResult($result): ResponseInterface
    {
        if ($result instanceof ResponseInterface) {
            // Response should be returned directly
            return $result;
        }

        if (is_object($result) && method_exists($result, '__toString')) {
            // Try to get string out of object as last fallback
            $result = $result->__toString();
        }

        if (is_string($result)) {
            // String supposed to be appended to response body
            return $this->responseFactory->createResponse()->append($result);
        }

        // arrays, non-stringable objects, resources are considered as invalid controller action results
        throw new \RuntimeException('Controller action result must be either ResponseInterface or string');
    }


}