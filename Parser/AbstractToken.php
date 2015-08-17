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


abstract class AbstractToken
{
    public $pos;
    public $name;

    public $rule;

    public $id;

    /**
     * AbstractToken constructor.
     */
    public function __construct($options)
    {
        if(isset($options[0])) {
            $this->name = $options[0];
        }

        foreach($options as $name => $value) {
            if(is_string($name)) {
                $this->{$name} = $value;
            }
        }
    }
}