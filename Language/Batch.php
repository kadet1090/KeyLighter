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

use Kadet\Highlighter\Matcher\CommentMatcher;
use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\Rule;

class Batch extends GreedyLanguage
{
    public function setupRules()
    {
        $this->rules->addMany([
            'comment' => [
                new Rule(new RegexMatcher('/^\s*(rem)[\t\n\r]+/mi')),
                new Rule(new RegexMatcher('/^\s*(rem\s+.+)/mi')),
                new Rule(new CommentMatcher(['::'])),
            ],

            'string' => new Rule(new RegexMatcher('/^\s*@?echo[ \t]+(.+)\s/mi'), ['priority' => 2]),

            'keyword' => [
                new Rule(new RegexMatcher('/^\s*(@?echo(\s+(on|off))?)\b/mi'), ['priority' => 3]),
                new Rule(new WordMatcher([
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
            ],

            'variable'  => [
                'assign' => new Rule(new RegexMatcher('/(\w+)[+-]?=/')),
                new Rule(new RegexMatcher('/(%\w+%)/i'), ['context' => ['*none', '*string'], 'priority' => 4]),
                new Rule(new RegexMatcher('/(%\w+)/i'), ['context' => ['*none', '*string'], 'priority' => 4]),
            ],

            'number'    => new Rule(new RegexMatcher('/(-?(?:0[0-7]+|0[xX][0-9a-fA-F]+|0b[01]+|\d+))/')),
            'delimiter' => new Rule(new RegexMatcher('/^(\$)/m')),

            'symbol.parameter' => new Rule(new RegexMatcher('/(\/[a-z])\b/i'), [
                'priority' => 0,
                'context'  => ['!comment'],
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
