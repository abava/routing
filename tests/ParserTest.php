<?php

class ParserTest extends PHPUnit_Framework_TestCase
{

    public function testReplacePatternMatchers()
    {
        $path = '{id:number}';
        $replaced = \Abava\Routing\Parser::replacePatternMatchers($path);
        $this->assertEquals('{id:[0-9]+}', $replaced);
    }

    public function testAddPatterMatcher()
    {
        \Abava\Routing\Parser::addPatternMatcher('test', 'abava');
        $path = '{id:test}';
        $replaced = \Abava\Routing\Parser::replacePatternMatchers($path);
        $this->assertEquals('{id:abava}', $replaced);
    }

    public function testParse()
    {
        $parser = new \Abava\Routing\Parser();
        $data = $parser->parse('{id:number}');
        $this->assertSame([[['id','[0-9]+']]], $data);
    }

}
