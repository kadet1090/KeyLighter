<?php

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

namespace Kadet\Highlighter\Parser\Validator;

use Kadet\Highlighter\Parser\Context;

class Validator
{
    public const CONTEXT_IN        = 1;
    private const CONTEXT_NOT_IN    = 2;
    private const CONTEXT_IN_ONE_OF = 4;
    private const CONTEXT_EXACTLY   = 8;
    private const CONTEXT_ON_TOP    = 16;
    private const CONTEXT_REGEX     = 32;

    private $_rules = [];

    /**
     * Validator constructor.
     *
     * @param array $rules
     */
    public function __construct(array $rules = [])
    {
        $this->setRules($rules);
    }

    public function validate(Context $context, $additional = [])
    {
        return $this->_validate($context->stack, $additional + $this->_rules);
    }

    public function setRules($rules)
    {
        if (empty($rules)) {
            $this->_rules = [ 'none' => Validator::CONTEXT_IN_ONE_OF ];
        } else {
            foreach ($rules as $key => $rule) {
                list($plain, $type)   = $this->_parse($rule);
                $this->_rules[$plain] = $type;
            }
        }
    }

    private function _clean($rule, &$required)
    {
        if (strpos($rule, '.') !== false) {
            foreach (
                array_filter(array_keys($required), function ($key) use ($rule) {
                    return fnmatch($key . '.*', $rule);
                }) as $remove
            ) {
                unset($required[$remove]);
            }
        }
    }

    protected function _validate($context, $rules, $result = false)
    {
        if (empty($context)) {
            $context = ['none'];
        }

        foreach ($rules as $rule => &$type) {
            $matched = $this->_matches($context, $rule, $type);

            if ($type & Validator::CONTEXT_NOT_IN) {
                if ($matched) {
                    return false;
                }
                $result = true;
            } elseif ($type & Validator::CONTEXT_IN) {
                if (!$matched) {
                    return false;
                }
                $result = true;

                $this->_clean($rule, $rules);
            } elseif ($type & Validator::CONTEXT_IN_ONE_OF) {
                if ($matched) {
                    $result = true;
                    $this->_clean($rule, $rules);
                }
            }
        }

        return $result;
    }

    private function _parse($rule)
    {
        $types = [
            '!' => Validator::CONTEXT_NOT_IN,
            '+' => Validator::CONTEXT_IN,
            '*' => Validator::CONTEXT_IN_ONE_OF,
            '@' => Validator::CONTEXT_EXACTLY,
//            '^' => Validator::CONTEXT_ON_TOP,
            '~' => Validator::CONTEXT_REGEX
        ];

        if (!isset($types[$rule[0]])) {
            return [$rule, Validator::CONTEXT_IN];
        }

        $type = 0;
        $pos  = 0;
        foreach (str_split($rule) as $pos => $char) {
            if (!isset($types[$char])) {
                break;
            }

            $type |= $types[$char];
        }

        $rule = substr($rule, $pos);

        if ($type & self::CONTEXT_REGEX) {
            $rule = "/^$rule(\\.\\w+)?/i";
        }

        return [$rule, $type];
    }

    private function _matches($context, $rule, $type)
    {
        if ($type & self::CONTEXT_EXACTLY) {
            return in_array($rule, $context, true);
        } elseif ($type & self::CONTEXT_REGEX) {
            foreach ($context as $item) {
                if (preg_match($rule, $item)) {
                    return true;
                }
            }
            return false;
        } else {
            if (in_array($rule, $context, true)) {
                return true;
            }

            foreach ($context as $item) {
                if (fnmatch("$rule.*", $item)) {
                    return true;
                }
            }
            return false;
        }
    }

    public static function everywhere()
    {
        static $validator;
        if (!$validator) {
            $validator = new DelegateValidator(function () {
                return true;
            });
        }

        return $validator;
    }
}
