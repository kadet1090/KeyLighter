<?php
/**
 * Highlighter
 *
 * Copyright (C) 2015, Some right reserved.
 *
 * @author Kacper "Kadet" Donat <kadet1090@gmail.com>
 *
 * Contact with author:
 * Xmpp: kadet@jid.pl
 * E-mail: kadet1090@gmail.com
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Utils;

class ConsoleHelper
{
    private $_stack   = [];
    private $_current = [];

    private $_default = [
        'color'      => 'default',
        'background' => 'default',
        'bold'       => false,
        'underlined' => false,
        'dim'        => false,
        'blink'      => false,
    ];

    public function styled($style, $text)
    {
        return $this->open($style).$text.$this->close();
    }

    public function open($style)
    {
        if (!empty($this->_current)) {
            $this->_stack[] = $this->_current;
            $style          = array_diff($style, $this->_current);
        }

        $this->_current = array_merge($this->_current, $style);

        return $this->_set(array_diff($this->_current, $this->_default));
    }

    public function close()
    {
        $this->_current = empty($this->_stack) ? $this->_default : array_pop($this->_stack);

        return "\033[0m".$this->_set(array_diff($this->_current, $this->_default));
    }

    private function _color($name, $bg = false)
    {
        $colors = [
            'default'       => 39,
            'black'         => 30,
            'red'           => 31,
            'green'         => 32,
            'yellow'        => 33,
            'blue'          => 34,
            'magenta'       => 35,
            'cyan'          => 36,
            'light gray'    => 37,
            'dark gray'     => 90,
            'light red'     => 91,
            'light green'   => 92,
            'light yellow'  => 93,
            'light blue'    => 94,
            'light magenta' => 95,
            'light cyan'    => 96,
            'white'         => 97,
        ];

        return $colors[strtolower($name)] + ($bg ? 10 : 0);
    }

    private function _style($name, $value)
    {
        switch ($name) {
            case 'color':
                return $this->_color($value);
            case 'background':
                return $this->_color($value, true);
            case 'bold':
                return $value ? 1 : 21;
            case 'dim':
                return $value ? 2 : 22;
            case 'underline':
                return $value ? 4 : 24;
            case 'blink':
                return $value ? 5 : 25;
            case 'invert':
                return $value ? 7 : 27;
        }

        return null;
    }

    private function _set($style)
    {
        $escape = "\e[".implode(';', array_map(function ($style, $name) {
            return $this->_style($style, $name);
        }, array_keys($style), $style)).'m';

        return $escape === "\e[m" ? null : $escape;
    }

    public function reset()
    {
        return "\e[0m";
    }
}
