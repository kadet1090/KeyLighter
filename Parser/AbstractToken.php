<?php
/**
 * Highlighter
 *
 * Copyright (C) 2015, Some right reserved.
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/legalcode CC BY-SA
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Parser;


use Kadet\Highlighter\Utils\Helper;

abstract class AbstractToken
{
    private static $_id = 0;


    public $pos;
    public $name;

    /**
     * @var Rule
     */
    public $rule;

    public $id;

    public $valid = true;

    /**
     * AbstractToken constructor.
     */
    public function __construct($options)
    {
        $this->id = (++self::$_id);

        // Name
        if(isset($options[0])) {
            $this->name = $options[0];
        }

        foreach($options as $name => $value) {
            if(is_string($name)) {
                $this->{$name} = $value;
            }
        }
    }

    public static function compare($a, $b)
    {
        if (!($a instanceof AbstractToken) || !($b instanceof AbstractToken)) {
            throw new \RuntimeException();
        }

        if ($a->pos == $b->pos) {
            if (get_class($a) == get_class($b)) {
                return Helper::cmp($b->rule->getPriority(), $a->rule->getPriority());
            }
            return $a instanceof EndToken ? -1 : 1;
        }

        return ($a->pos > $b->pos) ? 1 : -1;
    }
}