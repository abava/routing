<?php

namespace spec\Venta\Routing;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Venta\Contracts\Routing\Delegate;
use Venta\Contracts\Routing\Middleware;

class MiddlewarePipelineSpec extends ObjectBehavior
{
    function it_adds_middleware_and_processes_pipeline(
        ServerRequestInterface $request,
        Middleware $middleware,
        Delegate $delegate,
        ResponseInterface $delegateResponse,
        ResponseInterface $middlewareResponse
    ) {
        $delegate->next($request)->willReturn($delegateResponse);
        $middleware->process($request, $delegate)->willReturn($middlewareResponse);
        $pipeline = $this->withMiddleware($middleware);
        $pipeline->process($request, $delegate)->shouldBe($middlewareResponse);
        $this->process($request, $delegate)->shouldBe($delegateResponse);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(\Venta\Contracts\Routing\MiddlewarePipeline::class);
    }

    function it_processes_empty_pipeline(
        ServerRequestInterface $request,
        Delegate $delegate,
        ResponseInterface $response
    ) {
        $delegate->next($request)->willReturn($response);
        $this->process($request, $delegate)->shouldBe($response);
    }

    function it_respects_middleware_sequence(
        Middleware $m1,
        Middleware $m2,
        Middleware $m3,
        ServerRequestInterface $request,
        Delegate $delegate,
        ResponseInterface $response,
        ResponseInterface $lastResponse
    ) {
        $m1->process($request, Argument::type(Delegate::class))->will(function ($args) use ($lastResponse, $response) {
            $args[1]->next($args[0]);

            return $lastResponse;
        })->shouldBeCalled();
        $m2->process($request, Argument::type(Delegate::class))->will(function ($args) {
            return $args[1]->next($args[0]);
        })->shouldBeCalled();
        $m3->process($request, Argument::type(Delegate::class))->will(function ($args) {
            return $args[1]->next($args[0]);
        })->shouldBeCalled();
        $delegate->next($request)->willReturn($response)->shouldBeCalled();

        $this
            ->withMiddleware($m1)
            ->withMiddleware($m2)
            ->withMiddleware($m3)
            ->process($request, $delegate)->shouldBe($lastResponse);
    }

}
