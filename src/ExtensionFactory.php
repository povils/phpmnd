<?php

namespace Povils\PHPMND;

use Povils\PHPMND\Extension\Extension;

/**
 * Class ExtensionFactory
 *
 * @package Povils\PHPMND
 */
class ExtensionFactory
{
    /**
     * @param string $extensionName
     *
     * @return Extension
     * @throws \Exception
     */
    public static function create($extensionName)
    {
        $extensionClassName = 'Povils\PHPMND\Extension\\' . self::pascalCase($extensionName) . 'Extension';
        if (false === class_exists($extensionClassName)) {
            throw new \Exception(sprintf('Extension "%s" does not exist', $extensionName));
        }

        return new $extensionClassName;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private static function pascalCase($string)
    {
        return preg_replace_callback(
            '/(?:^|_)([a-z])/',
            function($match) {
                return strtoupper($match[1]);
            },
            $string
        );
    }
}
