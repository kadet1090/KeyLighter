# KeyLighter
Yet another Syntax Highlighter in PHP meant to be as extensible 
and easy to use as it only can, but with performance in mind.

## Name
Name "KeyLighter" is inspired by Key Light concept in photography and cinematography.

    The key light is the first and usually most important light that a photographer, 
    cinematographer, lighting cameraman, or other scene composer will use in a lighting setup. 
    The purpose of the key light is to highlight the form and dimension of the subject.

KeyLighter is supposed to do the same thing - for code.

# Requirements
 
PHP 5.4 and composer

Yep, that's all. You don't even need any not core PHP extensions.

## Why KeyLighter?
### It works on CLI! And more!
KeyLighter was originally designed as CLI highlighter for my own usage, 
but then I decided that it should be able to generate any possible output, 
currently supported are **HTML** via `\Kadet\Highlighter\Formatter\HtmlFormatter` 
and **CLI** via `\Kadet\Highlighter\Formatter\CliFormatter`

Example output for file (taken from wikipedia):
```php
<?php
function myAge($birthYear) {
    $yearsOld = date('Y') - $birthYear;
    return $yearsOld . ' year' . ($yearsOld != 1 ? 's' : '');
}

echo 'I am currently ' . myAge(1981) . ' old.';
```

#### Cli
![CLI](https://dl.dropboxusercontent.com/u/60020102/ShareX/2015-08/2015-08-28_16-52-26.png) 

#### HTML (Customizable with CSS)
![HTML](https://dl.dropboxusercontent.com/u/60020102/ShareX/2015-08/2015-08-28_16-57-10.png)
```html
<span class="language php">&lt;?php
<span class="keyword">function</span> <span class="symbol function">myAge</span>(<span class="variable">$birthYear</span>) {
    <span class="variable">$yearsOld</span> = date(<span class="string single">'Y'</span>) - <span class="variable">$birthYear</span><span class="operator punctuation">;</span>
    <span class="keyword">return</span> <span class="variable">$yearsOld</span> . <span class="string single">' year'</span> . (<span class="variable">$yearsOld</span> != <span class="number">1</span> ? <span class="string single">'s'</span> : <span class="string single">''</span>)<span class="operator punctuation">;</span>
}

<span class="keyword">echo</span> <span class="string single">'I am currently '</span> . myAge(<span class="number">1981</span>) . <span class="string single">' old.'</span><span class="operator punctuation">;</span>
```
#### Your own?
You can always write your own Formatter and use it for outputting data, 
I will describe writing formatters on wiki soon™.

### Simple to use
Just like any other composer library add `kadet/highlighter` to your 
`composer.json` require section, include autoload file and you're redy to go.

Simple usage example:
```php
<?php
$content = file_get_contents(isset($argv[1]) ? $argv[1] : __FILE__);

$parser = new \Kadet\Highlighter\Language\PhpLanguage($content);
$cli    = new \Kadet\Highlighter\Formatter\CliFormatter();
$html   = new \Kadet\Highlighter\Formatter\HtmlFormatter();

echo 'HTML:' . PHP_EOL . '<pre>' . $html->format($content, $parser->tokens()) . '</pre>';
echo 'CLI:'  . PHP_EOL . $cli->format($content, $parser->tokens());
```

Run with `php <^ that file> [file-to-highlight.php]`

### It's context sensitive 
Some of tokens are valid in some contexts, some not. This library 
is context sensitive and you can define when they are valid.

In this case context mean just "inside of other token", 
for example lets assume that `string` token is defined 
as everything from " to next " and `keyword` is 
defined as 'sit' substring.

```js
↓ string:start     ↓ keyword:start
"Lorem ipsum dolor sit amtet"
         keyword:end ↑      ↑ string:end
         
Token tree:
        
Token.name           Token.pos
------------------------------
string:start         0
    keyword:start    21
    keyword:end      23
string:end           30
```

So as you can see `keyword` is inside of `string`, 
and therefore is not valid and should be deleted. 
You can define tokens valid only in some context, or invalid in other.

Oh, and token names are cascade it means that `string.single` is `string`,
but `string` is not `string.single`.

Token validation rules will be described on wiki soon™.

### Write your own language definitions easily
One of my main goals was ability to easily add new language definitions.
Currently only supported languages are PHP and XML, 
mainly because I needed them for testing purposes.

For example XML definition looks like this:
```php
<?php
class XmlLanguage extends Language
{
    const TAG_REGEX = '\w+(?::\w+)?';

    public function getRules()
    {
        return [
            'tag.open' => [
                new OpenRule(new RegexMatcher('/(<)\w/')),
                new CloseRule(new SubStringMatcher('>'), ['priority' => -1])
            ],
            
            'symbol.tag'       => new Rule(new RegexMatcher('/<\\/?(' . self::TAG_REGEX . ')/'), ['context' => ['tag']]),
            'symbol.attribute' => new Rule(new RegexMatcher('/(' . self::TAG_REGEX . ')=/'), ['context' => ['tag']]),
            
            'string' => new Rule(new QuoteMatcher([
                'single' => "'",
                'double' => '"'
            ]), ['context' => ['tag']]),

            'tag.close' => new Rule(new RegexMatcher('/(<\\/' . self::TAG_REGEX . '>)/')),
        ];
    }

    public function getIdentifier()
    {
        return 'xml';
    }
}
```

I will try to write as many definitions as I only can, 
but any PRs are welcome.

### Fast ![Optimized for php7.0](https://img.shields.io/badge/optimized%20for-PHP%207-8892BF.svg)
Even though it wasn't supposed to be fastest code highlighter in PHP 
it is still quite fast, up to about 7.5x faster than [GeSHi](http://geshi.org/).
It performs best on PHP 7 (more than 2x faster than GeSHi in every case).
Unfortunately in some cases can be little bit slower than GeSHi (Some files on PHP 5.4)
You can find more about performance in [wiki](https://github.com/kadet1090/KeyLighter/wiki/Performance).

## Roadmap
There are still few things to do, you can find all of them (and even propose) on [trello](https://trello.com/b/9I4CO0Te/highlighter). Most important are:

### Tests
As it was supposed to be weekend project I didn't write any tests for it, but obviously now they are necessary. 

### Nesting languages
Many languages can be injected inside others, for example CSS or JS in HTML, It should be supported. 
Foundaments for that are done, it's possible to specify start and end tokens for languages, 
but not possible to parse many languages at once.

## Thanks
For [Maciej](https://github.com/ksiazkowicz), [Maciej](https://github.com/sobak) and Monika for all support, moral too.