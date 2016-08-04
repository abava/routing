<?php

use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /**
     * @test
     */
    public function canReplacePatternMatchers()
    {
        $path = '{id:number}';
        $replaced = \Abava\Routing\Parser::replacePatternMatchers($path);
        $this->assertEquals('{id:[0-9]+}', $replaced);
    }

    /**
     * @test
     */
    public function canAddPatterMatcher()
    {
        \Abava\Routing\Parser::addPatternMatcher('test', 'abava');
        $path = '{id:test}';
        $replaced = \Abava\Routing\Parser::replacePatternMatchers($path);
        $this->assertEquals('{id:abava}', $replaced);
    }

    /**
     * @test
     */
    public function canParse()
    {
        $parser = new \Abava\Routing\Parser();
        $data = $parser->parse('{id:number}');
        $this->assertSame([[['id','[0-9]+']]], $data);
    }

}
