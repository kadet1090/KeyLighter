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

namespace Kadet\Highlighter\Language;

use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Parser\Rule;

class Xaml extends Xml
{
    public function setupRules()
    {
        parent::setupRules();

        $attribute = $this->rules->rule('symbol.attribute');
        $tag = $this->rules->rule('symbol.tag');

        $this->rules->remove('symbol.attribute');
        $this->rules->remove('symbol.tag');

        $this->rules->add('variable', $attribute);
        $this->rules->add('symbol.class', $tag);
        $this->rules->add('variable.property', new Rule(new RegexMatcher('/[\w-]+\.([\w-]+)/'), [
            'context' => ['*tag', '*expression', '!string']
        ]));

        $this->rules->add('expression', new Rule(new RegexMatcher('/=["\']?(?P<expression>\{(?>(?:(?&expression)|[^{}]*)*)\})/'), [
            'context' => ['tag.open']
        ]));
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['xaml'],
            'mime'      => [],
            'extension' => ['*.xaml']
        ];
    }
}
