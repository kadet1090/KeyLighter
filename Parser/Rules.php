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

use Kadet\Highlighter\Exceptions\NameConflictException;
use Kadet\Highlighter\Exceptions\NoSuchElementException;
use Kadet\Highlighter\Language\Language;
use Kadet\Highlighter\Parser\Validator\Validator;

class Rules extends \ArrayObject
{
    /** @var Language Default language assigned to added rules. */
    private $_language;

    /** @var Validator Default validator used in added rules. */
    public $validator;

    private function _getName($name, $prefix)
    {
        if (is_int($name)) {
            return $prefix;
        } else {
            return $prefix ? "$prefix.$name" : $name;
        }
    }

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

    /**
     * Adds array of rules
     *
     * @param array       $rules
     * @param string|null $prefix
     *
     * @throws \LogicException
     */
    public function addMany(array $rules, $prefix = null)
    {
        foreach ($rules as $type => $rule) {
            $type = $this->_getName($type, $prefix);

            if ($rule instanceof Rule) {
                $this->add($type, $rule);
            } elseif (is_array($rule)) {
                $this->addMany($rule, $type);
            } else {
                throw new \LogicException('Array values has to be either arrays of rules or rules.');
            }
        }
    }

    /**
     * Adds one rule
     *
     * @param string $type
     * @param Rule   $rule
     *
     * @throws NameConflictException When there is already defined rule with given name.
     */
    public function add($type, Rule $rule)
    {
        if (!isset($this[$type])) {
            $this[$type] = [];
        }

        if ($rule->language === false) {
            $rule->language = $this->_language;
        }

        if ($rule->validator === false) {
            $rule->validator = $this->validator;
        }

        $rule->factory->setBase($type);

        if ($rule->name !== null) {
            if (isset($this[$type][$rule->name])) {
                throw new NameConflictException("Rule with '{$rule->name}' is already defined, name has to be unique!");
            }

            $this[$type][$rule->name] = $rule;
            return;
        }

        $this[$type][] = $rule;
    }

    /**
     * Return reference to rule of given type and index.
     *
     * @param string $type
     * @param mixed  $index
     *
     * @return \Kadet\Highlighter\Parser\Rule
     */
    public function &rule($type, $index = 0)
    {
        return $this[$type][$index];
    }

    /**
     * Retrieves all rules of given type.
     *
     * @param $type
     *
     * @return Rule[]
     * @throws NoSuchElementException
     */
    public function rules($type)
    {
        if (!isset($this[$type])) {
            throw new NoSuchElementException("There isn't any rule of '$type' type.");
        }

        return $this[$type];
    }

    /**
     * Replaces rule of given type and index with provided one.
     *
     * @param Rule $replacement
     * @param      $type
     * @param int  $index
     */
    public function replace(Rule $replacement, $type, $index = 0)
    {
        $current = $this->rule($type, $index);
        if ($current->name !== null) {
            $replacement->name = $current->name;
        }

        $this[$type][$index] = $replacement;
    }

    /**
     * Removes rule of given type and index.
     *
     * @param string $type
     * @param mixed  $index
     *
     * @throws NoSuchElementException
     */
    public function remove($type, $index = null)
    {
        if ($index === null) {
            unset($this[$type]);
            return;
        }

        if (!isset($this[$type][$index])) {
            throw new NoSuchElementException("There is no rule '$type' type indexed by '$index'.");
        }

        unset($this[$type][$index]);
    }

    /**
     * Retrieves all rule set.
     *
     * @return Rule[]
     */
    public function all()
    {
        $items = $this->getArrayCopy();
        if (empty($items)) {
            return [];
        }

        return call_user_func_array('array_merge', $items);
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
