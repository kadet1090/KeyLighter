<?php

declare(strict_types=1);

/**
 * Highlighter
 *
 * Copyright (C) 2016, Some right reserved.
 *
 * @author Kacper "Kadet" Donat <kacper@kadet.net>
 *
 * Contact with author:
 * Xmpp: me@kadet.net
 * E-mail: contact@kadet.net
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Utils;

trait Singleton
{
    /**
     * @var static
     */
    private static $_instance;

    /**
     * @return static
     */
    public static function get()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new static();
            self::$_instance->init();
        }

        return self::$_instance;
    }

    public function init() {}
}
