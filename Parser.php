<?php declare(strict_types = 1);

namespace Abava\Routing;

use FastRoute\RouteParser\Std;

/**
 * Class Parser
 *
 * @package Abava\Routing
 */
class Parser extends Std
{

    /**
     * Predefined pattern matcher array
     *
     * @var array
     */
    protected static $patternMatchers = [
        '/{(.+?):number}/'  => '{$1:[0-9]+}',
        '/{(.+?):word}/'    => '{$1:[a-zA-Z]+}',
        '/{(.+?):alphanum}/'=> '{$1:[a-zA-Z0-9-_]+}',
        '/{(.+?):slug}/'    => '{$1:[a-z0-9-]+}'
    ];

    /**
     * Adds pattern matcher
     *
     * @param $alias
     * @param $regex
     * @return void
     */
    public static function addPatternMatcher(string $alias, string $regex)
    {
        $pattern = '/{(.+?):' . $alias . '}/';
        $regex   = '{$1:' . $regex . '}';

        static::$patternMatchers[$pattern] = $regex;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($route)
    {
        return parent::parse(static::replacePatternMatchers($route));
    }

    /**
     * @param string $path
     * @return string
     */
    public static function replacePatternMatchers(string $path): string
    {
        return preg_replace(array_keys(static::$patternMatchers), array_values(static::$patternMatchers), $path);
    }
}