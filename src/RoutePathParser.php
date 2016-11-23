<?php declare(strict_types = 1);

namespace Venta\Routing;

use FastRoute\RouteParser\Std;
use Venta\Contracts\Routing\RoutePathParser as RoutePathParserContract;

final class RoutePathParser extends Std implements RoutePathParserContract
{
    /**
     * Default regex aliases.
     *
     * @var array
     */
    private static $regexAliases = [
        '/{(.+?):number}/'  => '{$1:[0-9]+}',
        '/{(.+?):word}/'    => '{$1:[a-zA-Z]+}',
        '/{(.+?):alphanum}/'=> '{$1:[a-zA-Z0-9-_]+}',
        '/{(.+?):slug}/'    => '{$1:[a-z0-9-]+}'
    ];

    /**
     * {@inheritdoc}
     */
    public static function addRegexAlias(string $alias, string $regexp)
    {
        static::$regexAliases['/{(.+?):' . $alias . '}/'] = '{$1:' . $regexp . '}';
    }

    /**
     * {@inheritdoc}
     */
    public function parse($route)
    {
        return parent::parse(static::replaceRegexAliases($route));
    }

    /**
     * {@inheritdoc}
     */
    public static function replaceRegexAliases(string $path): string
    {
        return preg_replace(array_keys(static::$regexAliases), array_values(static::$regexAliases), $path);
    }

}
