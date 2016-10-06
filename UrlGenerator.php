<?php declare(strict_types = 1);

namespace Venta\Routing;

use InvalidArgumentException;

/**
 * Class UrlGenerator
 *
 * @package Venta\Routing
 */
class UrlGenerator
{
    /**
     * Replacements in FastRoute are written as `{name}` or `{name:<pattern>}`;
     * this method uses a regular expression to search for substitutions that
     * match, and replaces them with the value provided.
     *
     * @param string $path
     * @param array $parameters
     * @return string
     * @throws InvalidArgumentException
     */
    public static function generate(string $path, array $parameters = []): string
    {
        $path = RouteParser::replacePatternMatchers($path);
        foreach ($parameters as $key => $value) {
            $pattern = sprintf(
                '~%s~x',
                sprintf('\{\s*%s\s*(?::\s*([^{}]*(?:\{(?-1)\}[^{}]*)*))?\}', preg_quote($key))
            );
            preg_match($pattern, $path, $matches);
            if (isset($matches[1]) && !preg_match('/' . $matches[1] . '/', (string)$value)) {
                throw new InvalidArgumentException(
                    "Substitution value '$value' does not match '$key' parameter '{$matches[1]}' pattern."
                );
            }
            $path = preg_replace($pattern, $value, $path);
        }
        // 1. remove patterns for named prameters
        // 2. remove optional segments' ending delimiters
        // 3. split path into an array of optional segments and remove those
        //    containing unsubstituted parameters starting from the last segment
        $path = preg_replace('/{(\w+):(.+?)}/', '{$1}', $path);
        $path = str_replace(']', '', $path);
        $segments = array_reverse(explode('[', $path));
        foreach ($segments as $n => $segment) {
            if (strpos($segment, '{') !== false) {
                if (isset($segments[$n - 1])) {
                    throw new InvalidArgumentException(
                        'Optional segments with unsubstituted parameters cannot '
                        . 'contain segments with substituted parameters when using FastRoute'
                    );
                }
                unset($segments[$n]);
                if (count($segments) == 0) {
                    preg_match('/{.+}/', $segment, $params);
                    $mandatory = $params[0] ?? $segment;
                    throw new InvalidArgumentException("Parameter '$mandatory' is mandatory");
                }
            }
        }
        $path = implode('', array_reverse($segments));

        return $path;
    }
}