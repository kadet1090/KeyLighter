<?php
/**
 * Created by PhpStorm.
 * User: k_don
 * Date: 16.08.2015
 * Time: 21:02
 */

namespace Kadet\Highlighter\Language;


use Kadet\Highlighter\Matcher\CommentMatcher;
use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Matcher\StringMatcher;
use Kadet\Highlighter\Matcher\WordMatcher;
use Kadet\Highlighter\Parser\Rule;

class PhpLanguage extends Language
{
    public function getRules()
    {
        return [
            'string' => new Rule(new StringMatcher([
                'single' => "'",
                'double' => '"'
            ])),
            'variable' => new Rule(new RegexMatcher('/[^\\\](\$[a-z_][a-z0-9_]*)/i'), [
                'context' => ['!string.single']
            ]),
            'variable.property' => new Rule(new RegexMatcher('/\$[a-z_][a-z0-9_]*->([a-z_][a-z0-9_]*)/i'), [
                'context'
            ]),
            'keyword' => new Rule(new WordMatcher([
                '__halt_compiler', 'abstract', 'and', 'array',
                'as', 'break', 'callable', 'case', 'catch',
                'class', 'clone', 'const', 'continue', 'declare',
                'default', 'die', 'do', 'echo', 'else', 'elseif',
                'empty', 'enddeclare', 'endfor', 'endforeach', 'endif',
                'endswitch', 'endwhile', 'eval', 'exit', 'extends',
                'final', 'finally', 'for', 'foreach', 'function',
                'global', 'goto', 'if', 'implements', 'include', 'include_once',
                'instanceof', 'insteadof', 'interface', 'isset', 'list',
                'namespace', 'new', 'or', 'print', 'private', 'protected',
                'public', 'require', 'require_once', 'return', 'static',
                'switch', 'throw', 'trait', 'try', 'unset',
                'use', 'var', 'while', 'xor', 'yield', '<?php', '?>'
            ]), ['context' => ['!string', '!variable', '!comment']]),
            'annotation' => new Rule(new RegexMatcher('/(@\w+)/i'), [
                'context' => ['comment.docblock']
            ]),
            'constant' => new Rule(new WordMatcher([
                '__CLASS__', '__DIR__', '__FILE__', '__FUNCTION__', 'self',
                '__LINE__', '__METHOD__', '__NAMESPACE__', '__TRAIT__'
            ])),
            'comment' => new Rule(new CommentMatcher(['//', '#'], [
                'docblock' => ['/**', '*/'],
                ['/*', '*/']
            ])),
            'number' => new Rule(new RegexMatcher('/((?:0[xbo]?)?\d+)/'))
        ];
    }
}