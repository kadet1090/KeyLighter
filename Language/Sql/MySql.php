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

namespace Kadet\Highlighter\Language\Sql;

use Kadet\Highlighter\Language\Sql;
use Kadet\Highlighter\Matcher\SubStringMatcher;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\ContextualToken;
use Kadet\Highlighter\Parser\TokenFactory;

class MySql extends Sql
{
    protected $_types = [
        '(?:tiny|small|medium|big)?int', 'integer', 'date', 'datetime', 'time', 'bool', 'unsigned', 'int[12348]?',
        '(?:tiny|medium|long)?text', '(?:tiny|medium|long)?blob', 'float[48]?', 'double(?:\s+precision)?', 'real', 'numeric',
        'dec', 'decimal', 'timestamp', 'year', '(?:var)?char', 'varbinary', 'varcharacter', 'enum', 'set', 'bit',
    ];

    protected $_keywords = [
        'add', 'all', 'alter', 'analyze', 'and', 'as', 'asc', 'asensitive', 'before', 'between', 'bigint', 'binary',
        'blob', 'both', 'by', 'call', 'cascade', 'case', 'change', 'check', 'collate', 'column', 'comment', 'charset',
        'condition', 'constraint', 'continue', 'convert', 'create', 'cross', 'current(?>_date|_time|_timestamp|_user)',
        'cursor', 'databases?', 'day(?>_hour|_(?:micro)?second|_minute)', 'dec', 'declare', 'default', 'delayed',
        'delete', 'desc', 'describe', 'deterministic', 'distinct', 'distinctrow', 'div', 'drop', 'dual', 'each', 'else',
        'elseif', 'enclosed', 'escaped', 'exists', 'exit', 'explain', 'fetch', 'flush', 'for', 'force', 'foreign',
        'from', 'fulltext', 'grant', 'group', 'having', 'high_priority', 'hour(?>_microsecond|_minute|_second)', 'if',
        'ignore', 'in', 'index', 'infile', 'inner', 'inout', 'insensitive', 'insert', 'interval', 'into', 'is',
        'iterate', 'join', 'key', 'keys', 'kill', 'leading', 'leave', 'left', 'like', 'limit', 'lines', 'load',
        'localtime(?:stamp)?', 'lock', 'long', 'loop', 'low_priority', 'match', 'minute_(micro)?second', 'mod',
        'modifies', 'natural', 'no_write_to_binlog', 'not', 'on', 'optimize', 'option', 'optionally', 'or', 'order',
        'out', 'outer', 'outfile', 'precision', 'primary', 'procedure', 'purge', 'raid0', 'reads?', 'references',
        'regexp', 'release', 'rename', 'repeat', 'replace', 'require', 'restrict', 'return', 'revoke', 'right', 'rlike',
        'schemas?', 'second_microsecond', 'select', 'sensitive', 'separator', 'set', 'show', 'soname', 'spatial',
        'specific', 'sql(?>_big_result|_calc_found_rows|_small_result|exception|state|warning)?', 'ssl', 'starting',
        'straight_join', 'table', 'terminated', 'then', 'to', 'trailing', 'trigger', 'undo', 'union', 'unique',
        'unlock', 'unsigned', 'update', 'usage', 'use', 'using', 'utc_(?>date|timestamp|time)', 'values', 'arying',
        'when', 'where', 'while', 'with', 'write', 'x509', 'xor', 'year_month', 'zerofill', 'auto_increment', 'engine'
    ];

    /**
     * Tokenization rules
     */
    public function setupRules()
    {
        parent::setupRules();
        $this->rules->add('symbol', new Rule(new SubStringMatcher('`'), [
            'factory' => new TokenFactory(ContextualToken::class)
        ]));
    }

    /** {@inheritdoc} */
    public function getIdentifier()
    {
        return 'mysql';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['mysql'],
            'mime'      => ['text/x-mysql'],
        ];
    }
}
