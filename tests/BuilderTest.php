<?php

use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{

    /**
     * @test
     */
    public function canCreateRoute()
    {
        $builder = new \Abava\Routing\Builder(['GET', 'HEAD']);
        $builder->url('/url');
        $builder->to('handle');
        $route = $builder->build();
        $this->assertInstanceOf(\Abava\Routing\Route::class, $route);
        $this->assertSame('/url', $route->getPath());
        $this->assertSame('handle', $route->getCallable());
        $this->assertSame(['GET', 'HEAD'], $route->getMethods());
    }

    /**
     * @test
     */
    public function canSetHostSchemeMiddlewareName()
    {
        $builder = new \Abava\Routing\Builder(['GET', 'HEAD']);
        $builder->url('/url')
                ->to('handle')
                ->host('localhost')
                ->scheme('http')
                ->name('named')
                ->middleware('middleware', $callback = function () {});
        $route = $builder->build();
        $this->assertSame('localhost', $route->getHost());
        $this->assertSame('http', $route->getScheme());
        $this->assertSame('named', $route->getName());
        $this->assertSame(['middleware' => $callback], $route->getMiddlewares());
    }

    /**
     * @test
     */
    public function canCreateMultipleRoutes()
    {
        $builder = new \Abava\Routing\Builder(['GET']);
        $builder->url('/url')->to('controller@action');
        $route1 = $builder->build();
        $route2 = $builder->build();

        $this->assertSame($route1->getPath(), $route2->getPath());
        $this->assertSame($route1->getCallable(), $route2->getCallable());
    }

    /**
     * @test
     */
    public function canChangeMethods()
    {
        $builder = new \Abava\Routing\Builder(['GET']);
        $builder->url('/url')->to('controller@action');
        $getRoute = $builder->build();
        $builder->methods(['POST']);
        $postRoute = $builder->build();

        $this->assertSame($getRoute->getPath(), $postRoute->getPath());
        $this->assertSame($getRoute->getCallable(), $postRoute->getCallable());
        $this->assertNotEquals($getRoute->getMethods(), $postRoute->getMethods());
    }

    /**
     * @test
     */
    public function canCleanNameAfterCreatingRoute()
    {
        $builder = new \Abava\Routing\Builder(['GET']);
        $builder->url('/url')->to('controller@action');
        $namedRoute = $builder->name('named')->build();
        $anonymousRoute = $builder->build();

        $this->assertSame('named', $namedRoute->getName());
        $this->assertEmpty($anonymousRoute->getName());
        $this->assertNotEquals($namedRoute->getName(), $anonymousRoute->getName());
    }

    /**
     * @test
     */
    public function canStaticGet()
    {
        $route = \Abava\Routing\Builder::get('/url')->to('handle')->build();
        $this->assertContains('GET', $route->getMethods());
    }

    /**
     * @test
     */
    public function canStaticPost()
    {
        $route = \Abava\Routing\Builder::post('/url')->to('handle')->build();
        $this->assertContains('POST', $route->getMethods());
    }

    /**
     * @test
     */
    public function canStaticPut()
    {
        $route = \Abava\Routing\Builder::put('/url')->to('handle')->build();
        $this->assertContains('PUT', $route->getMethods());
    }

    /**
     * @test
     */
    public function canStaticDelete()
    {
        $route = \Abava\Routing\Builder::delete('/url')->to('handle')->build();
        $this->assertContains('DELETE', $route->getMethods());
    }

    /**
     * @test
     */
    public function canStaticOptions()
    {
        $route = \Abava\Routing\Builder::options('/url')->to('handle')->build();
        $this->assertContains('OPTIONS', $route->getMethods());
    }

    /**
     * @test
     */
    public function canStaticPatch()
    {
        $route = \Abava\Routing\Builder::patch('/url')->to('handle')->build();
        $this->assertContains('PATCH', $route->getMethods());
    }

    /**
     * @test
     */
    public function canStaticHead()
    {
        $route = \Abava\Routing\Builder::head('/url')->to('handle')->build();
        $this->assertContains('HEAD', $route->getMethods());
    }

    /**
     * @test
     */
    public function canStaticAny()
    {
        $route = \Abava\Routing\Builder::any('/url')->to('handle')->build();
        $this->assertContains('HEAD', $route->getMethods());
        $this->assertContains('GET', $route->getMethods());
        $this->assertContains('POST', $route->getMethods());
        $this->assertContains('PUT', $route->getMethods());
        $this->assertContains('OPTIONS', $route->getMethods());
        $this->assertContains('PATCH', $route->getMethods());
        $this->assertContains('DELETE', $route->getMethods());
    }

}
