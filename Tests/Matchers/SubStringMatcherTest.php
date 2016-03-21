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

namespace Kadet\Highlighter\Tests\Matchers;

use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Tests\MatcherTestCase;

class SubStringMatcherTest extends MatcherTestCase
{
    public function testSubstringMatching()
    {
        $string  = 'test test2';
        $matcher = new SubStringMatcher('test');

        $this->assertTokens(
            [
                ['start', 'pos' => 0],
                ['end', 'pos' => 4],
                ['start', 'pos' => 5],
                ['end', 'pos' => 9],
            ], $matcher->match($string, $this->getFactory())
        );
    }
}
