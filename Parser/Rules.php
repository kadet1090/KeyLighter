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

namespace Kadet\Highlighter\Parser;


use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Parser\Validator\Validator;

class Rules extends \ArrayObject
{
    private $_language;
    public $validator;

    /**
     * Rules constructor.
     *
     * @param Language $language
     */
    public function __construct($language)
    {
        $this->_language = $language;
        $this->validator = new Validator();
    }

    public function addMany(array $rules, $prefix = null)
    {
        foreach ($rules as $name => $rule) {
            $name = $this->_getName($name, $prefix);

            if ($rule instanceof Rule) {
                $this->add($name, $rule);
            } elseif (is_array($rule)) {
                $this->addMany($rule, $name);
            } else {
                throw new \LogicException(); // todo: exception, message
            }
        }
    }

    private function _getName($name, $prefix)
    {
        if (is_int($name)) {
            return $prefix;
        } else {
            return $prefix ? "$prefix.$name" : $name;
        }
    }

    public function add($name, Rule $rule)
    {
        if (!isset($this[$name])) {
            $this[$name] = [];
        }

        if ($rule->language === false) {
            $rule->language = $this->_language;
        }

        if ($rule->validator === false) {
            $rule->validator = $this->validator;
        }

        $rule->factory->setBase($name);
        $this[$name][] = $rule;
    }

    /**
     * @param     $name
     * @param int $index
     *
     * @return \Kadet\Highlighter\Parser\Rule
     */
    public function &rule($name, $index = 0)
    {
        return $this[$name][$index];
    }

    /**
     * @param $name
     *
     * @return \Kadet\Highlighter\Parser\Rule[]
     */
    public function rules($name)
    {
        if (!isset($this[$name])) {
            throw new \InvalidArgumentException();
        }

        return $this[$name];
    }
    
    public function remove($name, $index = null)
    {
        if ($index === null) {
            unset($this[$name]);
        } else {
            unset($this[$name][$index]);
        }
    }

    public function all()
    {
        $items = $this->getArrayCopy();
        if(empty($items)) return [];

        return call_user_func_array('array_merge', array_values($items));
    }

    /**
     * @return Language
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language = null)
    {
        $this->_language = $language;
    }
}
