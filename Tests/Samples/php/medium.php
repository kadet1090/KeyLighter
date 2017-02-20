<?php
/**
 * Highlighter
 *
 * Actually this is keylighter BG (Before the Git)
 *
 * Copyright (C) 2016, Some right reserved.
 * @author Kacper "Kadet" Donat <kacper@kadet.net>
 *
 * Contact with author:
 * Xmpp: me@kadet.net
 * E-mail: contact@kadet.net
 *
 * From Kadet with love.
 */
class Parser
{
    const START = 1;
    const END   = 0;

    private $tokens = [

    ];

    private $text;

    /**
     * @var Rule[]
     */
    private $rules;

    /**
     * Parser constructor.
     * @param $text
     */
    public function __construct(array $text)
    {
        $this->text = $text;

        $this->rules = $this->getRules();
    }


    public function tokenize()
    {
        $tokens = [];
        foreach ($this->rules as $name => $rule) {
            $tokens[$name] = $rule->getMatcher()->match($this->text);
        }

        $this->tokens = $this->_flatTokens($tokens);

        $this->_fixTokens();

        $this->__dumpTokens();
    }

    public function getRules()
    {
        return [
            'string' => new Rule(new \Kadet\Highlighter\Matcher\QuoteMatcher([
                'single' => "'",
                'double' => '"'
            ])),
            'variable' => new Rule(new \Kadet\Highlighter\Matcher\RegexMatcher('/[^\\\](\$[a-z_][a-z0-9_]*)/i'), [
                'context' => ['!string.single']
            ]),
            'keywords' => new Rule(new \Kadet\Highlighter\Matcher\WordMatcher([
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
                'use', 'var', 'while', 'xor', 'yield', '<?php', '?>',
            ]), ['context' => ['!string']]),
            'constant' => new Rule(new \Kadet\Highlighter\Matcher\WordMatcher([
                '__CLASS__', '__DIR__', '__FILE__', '__FUNCTION__',
                '__LINE__', '__METHOD__', '__NAMESPACE__', '__TRAIT__'
            ]), ['context' => ['!string']]),
            'comment' => new Rule(new \Kadet\Highlighter\Matcher\CommentMatcher(['//', '#'], [
                'docblock' => ['/**', '*/'],
                //['/* ', '*/']
            ]), ['context' => ['!string']]),
        ];
    }
// test
# test2
    private function _flatTokens($array)
    {
        $result = [];
        foreach ($array as $name => $tokens) {
            foreach ($tokens as $token) {
                $token['name'] = $name . (isset($token[0]) ? '.' . $token[0] : '');
                $token['rule'] = $this->rules[$name];
                $result[]      = $token;
            }
        }

        return $result;
    }

    private function __dumpTokens()
    {
        foreach ($this->tokens as $token) {
            echo $token['name'] . ': ' . $token['start'] . ' => ' . $token['end'] . '  ';
            echo substr($this->text, $token['start'], $token['end'] - $token['start']);
            echo PHP_EOL;
        }
    }

    private function _fixTokens()
    {
        $list    = $this->_tokenList();
        $context = [];

        foreach ($list as $token) {
            /** @var Rule $rule */
            $rule = $token->token['rule'];

            if ($token->type === self::START) {
                if ($rule->validateContext($context)) {
                    $context[] = $token->name;
                } else {
                    unset($this->tokens[$token->id], $list[$token->end]);
                }
            } else {
                $context = array_diff($context, [$token->name]);
            }
        }
    }

    private function _tokenList()
    {
        $list = [];
        foreach ($this->tokens as $id => $token) {
            $list[] = (object)[
                'pos'   => $token['end'],
                'type'  => self::END,
                'name'  => $token['name'],
                'id'    => $id,
                'token' => $token
            ];

            $list[] = (object)[
                'pos'   => $token['start'],
                'type'  => self::START,
                'name'  => $token['name'],
                'id'    => $id,
                'end'   => count($list) - 1,
                'token' => $token
            ];
        }

        uasort($list, function ($a, $b) {
            if ($a->pos < $b->pos) {
                return -1;
            }

            return (int)($a->pos > $b->pos);
        });

        return $list;
    }
}

<<<'NOWDOC'
lel
NOWDOC;

<<<HEREDOC
lel
HEREDOC;

<<<TEST
<3
TEST;



$test  = '\\\\';
$test2 = '\' $wrongcontext ';
//$test2 = "$validcontext";