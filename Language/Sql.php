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

class Sql extends GreedyLanguage
{
    
    protected $_keywords = [
        'ADD', 'ALL', 'ALLOCATE', 'ALTER', 'AND', 'ANY', 'ARE', 'AS', 'ASENSITIVE', 'ASYMMETRIC', 'AT',
        'ATOMIC', 'AUTHORIZATION', 'BEGIN', 'BETWEEN', 'BOTH', 'BY', 'COMMENT',
        'CALL', 'CALLED', 'CASCADED', 'CASE', 'CAST', 'CHECK', 'CLOB', 'CLOSE', 'COLLATE',
        'COLUMN', 'COMMIT', 'CONDITION', 'CONNECT', 'CONSTRAINT', 'CONTINUE', 'CORRESPONDING', 'CREATE',
        'CROSS', 'CUBE', 'CURRENT', 'CURSOR', 'CYCLE', 'DEALLOCATE', 'DECLARE', 'DEFAULT', 'DELETE',
        'DEREF', 'DESCRIBE', 'DETERMINISTIC', 'DISCONNECT', 'DISTINCT', 'DO', 'DROP', 'DYNAMIC',
        'EACH', 'ELEMENT', 'ELSE', 'ELSEIF', 'END', 'ESCAPE', 'EXCEPT', 'EXEC', 'EXECUTE', 'EXISTS', 'EXIT',
        'EXTERNAL', 'FETCH', 'FILTER', 'FOR', 'FOREIGN', 'FREE', 'FROM', 'FULL', 'FUNCTION',
        'GET', 'GLOBAL', 'GRANT', 'GROUP', 'GROUPING', 'HANDLER', 'HAVING', 'HOLD', 'IDENTITY', 'IF',
        'IMMEDIATE', 'IN', 'INDICATOR', 'INNER', 'INOUT', 'INPUT', 'INSENSITIVE', 'INSERT',
        'INTERSECT', 'INTERVAL', 'INTO', 'IS', 'ITERATE', 'LANGUAGE', 'LARGE', 'LATERAL', 'LEADING',
        'LEAVE', 'LEFT', 'LIKE', 'LOCAL', 'LOOP', 'MATCH', 'MEMBER', 'MERGE', 'METHOD', 'MODIFIES',
        'MODULE', 'MONTH', 'MULTISET', 'NATIONAL', 'NATURAL', 'NCHAR', 'NCLOB', 'NEW', 'NO', 'NONE', 'NOT',
        'OF', 'OLD', 'ON', 'ONLY', 'OPEN', 'OR', 'ORDER', 'OUT','OUTER', 'OUTPUT', 'OVER',
        'OVERLAPS', 'PARAMETER', 'PARTITION', 'PRECISION', 'PREPARE', 'PRIMARY', 'PROCEDURE', 'RANGE', 'READS',
        'RECURSIVE', 'REF', 'REFERENCES', 'REFERENCING', 'RELEASE',
        'REPEAT', 'RESIGNAL', 'RESULT', 'RETURN', 'RETURNS', 'REVOKE', 'RIGHT', 'ROLLBACK', 'ROLLUP', 'ROW',
        'ROWS', 'SAVEPOINT', 'SCOPE', 'SCROLL', 'SEARCH', 'SECOND', 'SELECT', 'SENSITIVE', 'SESSION_USER',
        'SET', 'SIGNAL', 'SIMILAR', 'SOME', 'SPECIFIC', 'SPECIFICTYPE', 'SQL', 'SQLEXCEPTION',
        'SQLSTATE', 'SQLWARNING', 'START', 'STATIC', 'SUBMULTISET', 'SYMMETRIC', 'SYSTEM', 'SYSTEM_USER',
        'TABLE', 'TABLESAMPLE', 'THEN', 'TO', 'TRAILING', 'TRANSLATION', 'TREAT', 'TRIGGER', 'UNDO', 'UNION',
        'UNIQUE', 'UNKNOWN', 'UNNEST', 'UNTIL', 'UPDATE', 'USER', 'USING', 'VALUE', 'VALUES', 'VARYING', 'WHEN',
        'WHENEVER', 'WHERE', 'WHILE', 'WINDOW', 'WITH', 'WITHIN', 'WITHOUT', 'KEY', 'ACTION'
    ];

    protected $_types = [
        'ARRAY', 'BIGINT', 'BINARY', 'BIT', 'BLOB', 'BOOLEAN', 'CHAR', 'CHARACTER', 'DATE',
        'DEC', 'DECIMAL', 'FLOAT', 'INT', 'INTEGER', 'INTERVAL', 'NUMBER', 'NUMERIC', 'REAL',
        'SERIAL', 'SMALLINT', 'VARCHAR', 'VARYING', 'INT8', 'SERIAL8', 'TEXT'
    ];

    /**
     * Tokenization rules
     */
    public function setupRules()
    {
        $this->rules->addMany([
            'keyword'     => new Rule(new WordMatcher($this->_keywords, ['escape' => false])),
            'symbol.type' => new Rule(new WordMatcher($this->_types, ['escape' => false])),

            'constant' => new Rule(new WordMatcher(['FALSE', 'TRUE', 'NULL'])),
            'comment'  => new Rule(new CommentMatcher(['#', '--'], [['/*', '*/']])),
            
            'string'   => CommonFeatures::strings(['single' => '\'', 'double' => '"']),

            'number'          => new Rule(new RegexMatcher('/\b(-?\d+)\b/i')),
            'call'            => new Rule(new RegexMatcher('/([a-z_]\w*)\s*\(/i'), ['priority' => -1]),
            'operator.escape' => new Rule(new RegexMatcher('/(\\[\\0\'|bnrtZ%_])/'), ['context' => ['string']])
        ]);
    }

    /** {@inheritdoc} */
    public function getIdentifier()
    {
        return 'sql';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['sql'],
            'mime'      => ['text/x-sql'],
            'extension' => ['*.sql']
        ];
    }
}
