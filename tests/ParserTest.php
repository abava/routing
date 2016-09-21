<?php

use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /**
     * @test
     */
    public function canAddPatterMatcher()
    {
        \Venta\Routing\Parser::addPatternMatcher('test', 'venta');
        $path = '{id:test}';
        $replaced = \Venta\Routing\Parser::replacePatternMatchers($path);
        $this->assertEquals('{id:venta}', $replaced);
    }

    /**
     * @test
     */
    public function canParse()
    {
        $parser = new \Venta\Routing\Parser();
        $data = $parser->parse('{id:number}');
        $this->assertSame([[['id', '[0-9]+']]], $data);
    }

    /**
     * @test
     */
    public function canReplacePatternMatchers()
    {
        $path = '{id:number}';
        $replaced = \Venta\Routing\Parser::replacePatternMatchers($path);
        $this->assertEquals('{id:[0-9]+}', $replaced);
    }

}
