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

namespace Kadet\Highlighter\Language;

use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\LanguageToken;
use Kadet\Highlighter\Parser\Token\Token;
use Kadet\Highlighter\Parser\TokenFactory;
use Kadet\Highlighter\Parser\Validator\Validator;

class Batch extends GreedyLanguage
{
    public function setupRules()
    {
        $this->rules->addMany([
            'comment' => [
                new Rule(new RegexMatcher('/^\s*(rem)[\t\n\r]+/mi')),
                new Rule(new RegexMatcher('/^\s*(rem\s+.+)/mi')),
            ],

            'string' => CommonFeatures::strings(['single' => '\'', 'double' => '"']),

            'keyword.special' => new Rule(new RegexMatcher('/^\s*(@?echo(\s+(on|off))?)\b/mi'), ['priority' => 2]),

            'keyword' => new Rule(new WordMatcher([
                'ASSOC', 'ATTRIB', 'BREAK', 'BCDEDIT', 'CACLS', 'CD', 'CHCP', 'CHDIR', 'CHKDSK', 'CHKNTFS',
                'CLS', 'CMD', 'COLOR', 'COMP', 'COMPACT', 'CONVERT', 'COPY', 'DATE', 'DEL', 'DIR', 'DISKCOMP',
                'DISKCOPY', 'DISKPART', 'DOSKEY', 'DRIVERQUERY', 'ECHO', 'ENDLOCAL', 'ERASE', 'EXIT', 'FC',
                'FIND', 'FINDSTR', 'FOR', 'FORMAT', 'FSUTIL', 'FTYPE', 'GPRESULT', 'GRAFTABL', 'HELP', 'ICACLS',
                'IF', 'LABEL', 'MD', 'MKDIR', 'MKLINK', 'MODE', 'MORE', 'MOVE', 'OPENFILES', 'PATH', 'PAUSE',
                'POPD', 'PRINT', 'PROMPT', 'PUSHD', 'RD', 'RECOVER', 'REN', 'RENAME', 'REPLACE', 'RMDIR',
                'ROBOCOPY', 'SET', 'SETLOCAL', 'SC', 'SCHTASKS', 'SHIFT', 'SHUTDOWN',  'SORT', 'START',
                'SUBST', 'SYSTEMINFO', 'TASKLIST', 'TASKKILL', 'TIME', 'TITLE', 'TREE', 'TYPE', 'VER',
                'VERIFY', 'VOL', 'XCOPY', 'WMIC', 'CSCRIPT',
                'echo', 'set', 'for', 'if', 'exit', 'else', 'do', 'not', 'defined', 'exist',
            ]), ['priority' => 3]),

            'variable'  => [
                'assign' => new Rule(new RegexMatcher('/(\w+)[+-]?=/')),
                new Rule(new RegexMatcher('/(\$\w+)/i'), ['context' => ['*none', '*string.double']]),
                'special'  => new Rule(new RegexMatcher('/(\$[#@_])/i'), ['context' => ['*none', '*string.double']]),
                'argument' => new Rule(new RegexMatcher('/(\$\d+)/i'), ['context' => ['*none', '*string.double']]),
                new Rule(new RegexMatcher('/\$\{(\w+)(.*?)\}/i', [ 1 => Token::NAME, 2 => 'string' ]), ['context' => ['*none', '*string.double']])
            ],

            'number'    => new Rule(new RegexMatcher('/(-?(?:0[0-7]+|0[xX][0-9a-fA-F]+|0b[01]+|\d+))/')),
            'delimiter' => new Rule(new RegexMatcher('/^(\$)/m')),

            'expression' => [
                new Rule(new RegexMatcher('/(?=(\$\(((?>[^$()]+|(?1))+)\)))/x'), [
                    'context' => Validator::everywhere(),
                    'factory' => new TokenFactory(LanguageToken::class),
                    'inject'  => $this
                ]),
            ],

            'operator.escape' => new Rule(new RegexMatcher('/(\\\(?:x[0-9a-fA-F]{1,2}|u\{[0-9a-fA-F]{1,6}\}|[0-7]{1,3}|.))/i'), [
                'context' => ['*']
            ]),
        ]);
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'batch';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['bat', 'batch', 'dos'],
            'mime'      => ['application/bat', 'application/x-bat', 'application/x-msdos-program'],
            'extension' => ['*.bat', '*.cmd']
        ];
    }
}