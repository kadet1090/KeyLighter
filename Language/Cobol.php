<?php


namespace Kadet\Highlighter\Language;


use Kadet\Highlighter\Matcher\CommentMatcher;
use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\Rule;

class Cobol extends GreedyLanguage
{

    /**
     * Tokenization rules setup
     */
    public function setupRules()
    {
        $this->rules->addMany([
            'keyword' => new Rule(new WordMatcher([
                'ACCEPT', 'ACCESS', 'ADD', 'ADDRESS', 'ADVANCING', 'AFTER', 'ALL', 'ALPHABET',
                'ALPHABETIC(-(LOWER|UPPER))?', 'ALPHANUMERIC(-EDITED)?', 'ALSO', 'ALTER', 'ALTERNATE', 'AND', 'ANY',
                'APPLY', 'ARE', 'AREA', 'AREAS', 'ASCENDING', 'ASSIGN', 'AT', 'AUTHOR', 'BASIS', 'BEFORE', 'BEGINNING',
                'BINARY', 'BLANK', 'BLOCK', 'BOTTOM', 'BY', 'CALL', 'CANCEL', 'CBL', 'CHARACTER', 'CHARACTERS', 'CLASS',
                'CLOSE', 'CODE-SET', 'COLLATING', 'COMMA', 'COMMON', 'COMP(-[1-4])?', 'COMPUTATIONAL(-[1-4])?',
                'COMPUTE', 'CONFIGURATION', 'CONTAINS', 'CONTENT', 'CONTINUE', 'CONVERTING', 'COPY', 'CORR',
                'CORRESPONDING', 'COUNT', 'CURRENCY', 'DATA', 'DATE(-(COMPILED|WRITTEN))?', 'DAY-OF-WEEK', 'DAY',
                'DBCS', 'DEBUG-ITEM', 'DEBUGGING', 'DECIMAL-POINT', 'DECLARATIVES', 'DEGUGGING', 'DELETE', 'DELIMITED',
                'DELIMITER', 'DEPENDING', 'DESCENDING', 'DISPLAY(-1)?', 'DIVIDE', 'DIVISION', 'DOWN', 'DUPLICATES',
                'DYNAMIC', 'EBCDIC', 'EGCS', 'EJECT', 'ELSE',
                'END(-(ADD|CALL|COMPUTE|DELETE|DIVIDE|EVALUATE|IF|MULTIPLY|OF-PAGE|PERFORM|READ|RETURN|REWRITE|SEARCH|START|STRING|SUBTRACT|UNSTRING|WRITE))?',
                'ENDING', 'ENTER', 'ENTRY', 'ENVIRONMENT', 'EOP', 'EQUAL', 'ERROR', 'EVALUATE', 'EVERY', 'EXCEPTION',
                'EXIT', 'EXTEND', 'EXTERNAL', 'F', 'FALSE', 'FD', 'FILE(-CONTROL)?', 'FILLER', 'FIRST', 'FOOTING',
                'FOR', 'FROM', 'GIVING', 'GLOBAL', 'GO', 'GOBACK', 'GREATER', 'HIGH-VALUES?', 'I-O(-CONTROL)?', 'ID',
                'IDENTIFICATION', 'IF', 'IN', 'INDEX', 'INDEXED', 'INITIAL', 'INITIALIZE', 'INPUT', 'INPUT-OUTPUT',
                'INSERT', 'INSPECT', 'INSTALLATION', 'INTO', 'INVALID', 'IS', 'JUST', 'JUSTIFIED', 'KANJI', 'KEY',
                'LABEL', 'LEADING', 'LEFT', 'LENGTH', 'LESS', 'LINAGE(-COUNTER)?', 'LINE', 'LINES', 'LINKAGE', 'LIST',
                'LOCK', 'LOW-VALUES?', 'MAP', 'MEMORY', 'MERGE', 'MODE', 'MODULES', 'MORE-LABELS', 'MOVE', 'MULTIPLE',
                'MULTIPLY', 'NATIVE', 'NEGATIVE', 'NEXT', 'NO', 'NOLIST', 'NOMAP', 'NOSOURCE', 'NOT', 'NULL', 'NULLS',
                'NUMERIC(-EDITED)?', 'OBJECT-COMPUTER', 'OCCURS', 'OF', 'OFF', 'OMITTED', 'ON', 'OPEN', 'OPTIONAL',
                'OR', 'ORDER', 'ORGANIZATION', 'OTHER', 'OUTPUT', 'OVERFLOW', 'PACKED-DECIMAL', 'PADDING', 'PAGE',
                'PASSWORD', 'PERFORM', 'PIC', 'PICTURE', 'POINTER', 'POSITION', 'POSITIVE', 'PROCEDURE', 'PROCEDURES',
                'PROCEED', 'PROCESS', 'PROGRAM(-ID)?', 'QUOTE', 'QUOTES', 'RANDOM', 'READ', 'READY', 'RECORD',
                'RECORDING', 'RECORDS', 'REDEFINES', 'REEL', 'REFERENCE', 'RELATIVE', 'RELEASE', 'RELOAD', 'REMAINDER',
                'REMOVAL', 'RENAMES', 'REPLACE', 'REPLACING', 'RERUN', 'RESERVE', 'RESET', 'RETURN(-CODE)?REVERSED',
                'REWIND', 'REWRITE', 'RIGHT', 'ROUNDED', 'RUN', 'S', 'SAME', 'SD', 'SEARCH', 'SECTION', 'SECURITY',
                'SEGMENT-LIMIT', 'SELECT', 'SENTENCE', 'SEPARATE', 'SEQUENCE', 'SEQUENTIAL', 'SERVICE', 'SET',
                'SHIFT-(IN|OUT)', 'SIGN', 'SIZE', 'SKIP1', 'SKIP2', 'SKIP3',
                'SORT(-(CONTROL|CORE-SIZE|FILE-SIZE|MERGE|MESSAGE|MODE-SIZE|RETURN))?', 'SOURCE(-COMPUTER)?', 'SPACE',
                'SPACES', 'SPECIAL-NAMES', 'STANDARD(-[1-2])?', 'START', 'STATUS', 'STOP', 'STRING', 'SUBTRACT',
                'SUPPRESS', 'SYMBOLIC', 'SYNC', 'SYNCHRONIZED', 'TALLY', 'TALLYING', 'TAPE', 'TEST', 'THAN', 'THEN',
                'THROUGH', 'THRU', 'TIME', 'TIMES', 'TITLE', 'TO', 'TOP', 'TRACE', 'TRAILING', 'TRUE', 'U', 'UNIT',
                'UNSTRING', 'UNTIL', 'UP', 'UPON', 'USAGE', 'USE', 'USING', 'V', 'VALUE', 'VALUES', 'VARYING',
                'WHEN(-COMPILED)?', 'WITH', 'WORDS', 'WORKING-STORAGE', 'WRITE(-ONLY)?', 'ZERO(S|ES)?',
            ], ['case-sensitivity' => true, 'separated' => true, 'escape' => false])),
            'number'  => new Rule(new RegexMatcher('/([\+\-]?(?:[0-9]*[\.][0-9]+|[0-9]+))/')),
            'comment' => new Rule(new CommentMatcher(['*'])),
            'string'  => CommonFeatures::strings(['"', '\'']),
        ]);
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return "cobol";
    }


    public static function getMetadata()
    {
        return [
            'name'      => ['cobol'],
            'mime'      => ['text/x-cobol'],
            'extension' => ['*.cbl'],
        ];
    }
}