<?php declare(strict_types = 1);

namespace Venta\Routing\Strategy;

use ArrayObject;
use JsonSerializable;
use Psr\Http\Message\ResponseInterface;
use Venta\Contracts\Container\Container;
use Venta\Contracts\Routing\Strategy;
use Venta\Http\Factory\ResponseFactory;
use Venta\Routing\Route;

/**
 * Class Generic
 *
 * @package Venta\Routing\Strategy
 */
class Generic implements Strategy
{

    /**
     * Caller instance to create controller and call action
     *
     * @var \Venta\Contracts\Container\Container
     */
    protected $container;

    /**
     * Response factory will create new response instance if controller action will not return any
     *
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * Generic strategy constructor.
     *
     * @param \Venta\Contracts\Container\Container $container
     * @param ResponseFactory $responseFactory
     */
    public function __construct(Container $container, ResponseFactory $responseFactory)
    {
        $this->container = $container;
        $this->responseFactory = $responseFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(Route $route): ResponseInterface
    {
        $response = $this->container->call($route->getCallable(), $route->getParameters());

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

        if ($this->shouldBeJson($result)) {
            // Returns JSON response in case of arrayed data
            return $this->responseFactory->createJsonResponse($result);
        }

        if (is_string($result)) {
            // String supposed to be appended to response body
            return $this->responseFactory->createResponse()->append($result);
        }

        // arrays, non-stringable objects, resources are considered as invalid controller action results
        throw new \RuntimeException('Action result must be either ResponseInterface or string');
    }

    /**
     * Defines, if response should be JSON response, based on content body data type
     *
     * @param  mixed $content
     * @return bool
     */
    protected function shouldBeJson($content)
    {
        return $content instanceof JsonSerializable
               || $content instanceof ArrayObject
               || is_array($content);
    }
}