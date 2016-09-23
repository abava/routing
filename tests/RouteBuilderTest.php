<?php

use PHPUnit\Framework\TestCase;

class RouteBuilderTest extends TestCase
{

    /**
     * @test
     */
    public function canChangeAction()
    {
        $builder = new \Venta\Routing\RouteBuilder(['GET'], '/url', 'handle');
        $handleRoute = $builder->build();
        $builder->action('callback');
        $callbackRoute = $builder->build();

        $this->assertSame($handleRoute->getPath(), $callbackRoute->getPath());
        $this->assertNotSame($handleRoute->getCallable(), $callbackRoute->getCallable());
        $this->assertSame($handleRoute->getMethods(), $callbackRoute->getMethods());
    }

    /**
     * @test
     */
    public function canChangeMethods()
    {
        $builder = new \Venta\Routing\RouteBuilder(['GET'], '/url', 'handle');
        $getRoute = $builder->build();
        $builder->method('POST');
        $postRoute = $builder->build();

        $this->assertSame($getRoute->getPath(), $postRoute->getPath());
        $this->assertSame($getRoute->getCallable(), $postRoute->getCallable());
        $this->assertNotSame($getRoute->getMethods(), $postRoute->getMethods());
    }

    /**
     * @test
     */
    public function canChangePath()
    {
        $builder = new \Venta\Routing\RouteBuilder(['GET'], '/url', 'handle');
        $urlRoute = $builder->build();
        $builder->path('/prefix/url');
        $prefixRoute = $builder->build();

        $this->assertNotSame($urlRoute->getPath(), $prefixRoute->getPath());
        $this->assertSame($urlRoute->getCallable(), $prefixRoute->getCallable());
        $this->assertSame($urlRoute->getMethods(), $prefixRoute->getMethods());
    }

    /**
     * @test
     */
    public function canCleanNameAfterCreatingRoute()
    {
        $builder = new \Venta\Routing\RouteBuilder(['GET'], '/url', 'handle');
        $namedRoute = $builder->name('named')->build();
        $anonymousRoute = $builder->build();

        $this->assertSame('named', $namedRoute->getName());
        $this->assertEmpty($anonymousRoute->getName());
        $this->assertNotEquals($namedRoute->getName(), $anonymousRoute->getName());
    }

    /**
     * @test
     */
    public function canCreateMultipleRoutes()
    {
        $builder = new \Venta\Routing\RouteBuilder(['GET'], '/url', 'handle');
        $route1 = $builder->build();
        $route2 = $builder->build();

        $this->assertSame($route1->getPath(), $route2->getPath());
        $this->assertSame($route1->getCallable(), $route2->getCallable());
    }

    /**
     * @test
     */
    public function canCreateRoute()
    {
        $builder = new \Venta\Routing\RouteBuilder(['GET', 'HEAD'], '/url', 'handle');
        $route = $builder->build();
        $this->assertInstanceOf(\Venta\Routing\Route::class, $route);
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handle', $route->getCallable());
        $this->assertSame(['GET', 'HEAD'], $route->getMethods());
    }

    /**
     * @test
     */
    public function canCreateStatic()
    {
        $builder = new \Venta\Routing\RouteBuilder(['GET', 'HEAD'], '/url', 'handle');
        $staticBuilder = \Venta\Routing\RouteBuilder::create(['GET', 'HEAD'], '/url', 'handle');

        $route = $builder->build();
        $staticRoute = $staticBuilder->build();

        $this->assertSame($route->getPath(), $staticRoute->getPath());
        $this->assertSame($route->getCallable(), $staticRoute->getCallable());
        $this->assertSame($route->getMethods(), $staticRoute->getMethods());
    }

    /**
     * @test
     */
    public function canSetHostSchemeMiddlewareName()
    {
        $builder = new \Venta\Routing\RouteBuilder(['GET', 'HEAD'], '/url', 'handle');
        $builder->host('localhost')
                ->scheme('http')
                ->name('named')
                ->middleware('middleware', $callback = function () {
                });
        $route = $builder->build();
        $this->assertSame('localhost', $route->getHost());
        $this->assertSame('http', $route->getScheme());
        $this->assertSame('named', $route->getName());
        $this->assertSame(['middleware' => $callback], $route->getMiddlewares());
    }

    /**
     * @test
     */
    public function canStaticAny()
    {
        $route = \Venta\Routing\RouteBuilder::any('/url', 'handle')->build();
        $this->assertContains('HEAD', $route->getMethods());
        $this->assertContains('GET', $route->getMethods());
        $this->assertContains('POST', $route->getMethods());
        $this->assertContains('PUT', $route->getMethods());
        $this->assertContains('OPTIONS', $route->getMethods());
        $this->assertContains('PATCH', $route->getMethods());
        $this->assertContains('DELETE', $route->getMethods());
    }

    /**
     * @test
     */
    public function canStaticDelete()
    {
        $route = \Venta\Routing\RouteBuilder::delete('/url', 'handle')->build();
        $this->assertContains('DELETE', $route->getMethods());
    }

    /**
     * @test
     */
    public function canStaticGet()
    {
        $route = \Venta\Routing\RouteBuilder::get('/url', 'handle')->build();
        $this->assertContains('GET', $route->getMethods());
    }

    /**
     * @test
     */
    public function canStaticHead()
    {
        $route = \Venta\Routing\RouteBuilder::head('/url', 'handle')->build();
        $this->assertContains('HEAD', $route->getMethods());
    }

    /**
     * @test
     */
    public function canStaticOptions()
    {
        $route = \Venta\Routing\RouteBuilder::options('/url', 'handle')->build();
        $this->assertContains('OPTIONS', $route->getMethods());
    }

    /**
     * @test
     */
    public function canStaticPatch()
    {
        $route = \Venta\Routing\RouteBuilder::patch('/url', 'handle')->build();
        $this->assertContains('PATCH', $route->getMethods());
    }

    /**
     * @test
     */
    public function canStaticPost()
    {
        $route = \Venta\Routing\RouteBuilder::post('/url', 'handle')->build();
        $this->assertContains('POST', $route->getMethods());
    }

    /**
     * @test
     */
    public function canStaticPut()
    {
        $route = \Venta\Routing\RouteBuilder::put('/url', 'handle')->build();
        $this->assertContains('PUT', $route->getMethods());
    }

}
