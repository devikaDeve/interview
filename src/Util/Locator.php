<?php

namespace Skaleet\Interview\Util;

use DI\Container;

class Locator
{
    private static ?Container $container = null;

    public static function container(): Container
    {
        if (!self::$container) {
            self::$container = new Container();
        }

        return self::$container;
    }
}