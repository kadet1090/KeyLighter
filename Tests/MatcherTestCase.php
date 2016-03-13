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

namespace Kadet\KeyLighter\Tests;

require_once __DIR__.'/Constraint/TokensMatches.php';

use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\KeyLighter\Tests\Constraint\TokensMatches;
use PHPUnit_Framework_Exception;

class MatcherTestCase extends \PHPUnit_Framework_TestCase
{
    public function assertTokens($expected, $actual, $message = '') {
        if(count($expected) !== count($actual)) {
            throw new PHPUnit_Framework_Exception('Token count mismatches');
        }

        $constraint = new TokensMatches($expected);

        self::assertThat($actual, $constraint, $message);
    }

    public function getFactory() {
        return new TokenFactory('Kadet\Highlighter\Parser\Token');
    }
}