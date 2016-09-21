<?php

use PHPUnit\Framework\TestCase;

class MiddlewareValidatorTraitTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function canValidateMiddleware()
    {
        $validator = new class
        {
            use \Venta\Routing\Middleware\ValidatorTrait;
        };

        $middleware = Mockery::mock(\Venta\Routing\Contract\Middleware::class);

        // these are valid middlewares
        $this->assertTrue($validator->isValidMiddleware($middleware));
        $this->assertTrue($validator->isValidMiddleware(get_class($middleware)));
        $this->assertTrue($validator->isValidMiddleware(function () {
        }));

        // $object is not instance of Middleware contract
        $this->assertFalse($validator->isValidMiddleware(new stdClass()));
        // class is not subclass of Middleware contract
        $this->assertFalse($validator->isValidMiddleware(stdClass::class));
    }

}
