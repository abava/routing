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
    protected $patternMatchers = [
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
     * @return Parser
     */
    public function addPatternMatcher(string $alias, string $regex): Parser
    {
        $pattern = '/{(.+?):' . $alias . '}/';
        $regex   = '{$1:' . $regex . '}';

        $this->patternMatchers[$pattern] = $regex;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($route)
    {
        $route = preg_replace(array_keys($this->patternMatchers), array_values($this->patternMatchers), $route);
        return parent::parse($route);
    }


}