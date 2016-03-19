![Logo](http://kadet.net/keylighter/logo.png)
# KeyLighter [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/kadet1090/keylighter.svg?style=flat-square)](https://scrutinizer-ci.com/g/kadet1090/KeyLighter/?branch=master) ![Build](https://img.shields.io/scrutinizer/build/g/kadet1090/keylighter.svg?style=flat-square) ![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/kadet1090/keylighter.svg?style=flat-square)

Yet another Syntax Highlighter in PHP meant to be as extensible 
and easy to use as it only can, but with performance in mind.

## Name
Name "KeyLighter" is inspired by Key Light concept in photography and cinematography.

    The key light is the first and usually most important light that a photographer, 
    cinematographer, lighting cameraman, or other scene composer will use in a lighting setup. 
    The purpose of the key light is to highlight the form and dimension of the subject.

KeyLighter is supposed to do the same thing - for code.

# Installation
```
$ composer require kadet/keylighter
```

To use KeyLighter you just need PHP 5.5 or later, no special extensions required.

## Why KeyLighter?

### Simple to use
```php
use Kadet\Highlighter\Language;

echo \Kadet\Highlighter\highlight($source, new Language\Php(), $formatter);
// or
echo \Kadet\Highlighter\KeyLighter::get()->highlight($source, new Language\Php(), $formatter);
// or
$keylighter = new \Kadet\Highlighter\KeyLighter([options]);
echo $keylighter->highlight($source, new Language\Php(), $formatter);
```

You can find all available languages [here](https://github.com/kadet1090/KeyLighter/tree/master/Language) and formatters [here](https://github.com/kadet1090/KeyLighter/tree/master/Formatter).

### It works on CLI! And more!
**KeyLighter** was originally designed as CLI highlighter for my own usage,
but then I decided that it should be able to generate any possible output,
currently supported:

#### Cli `\Kadet\Highlighter\Formatter\CliFormatter`
![CLI](http://kadet.net/keylighter/php-cli.png)

It can even be styled, default styles are stored in `Styles\Cli\Default.php`, but you can just pass additional argument into constructor:

```php
new \Kadet\Highlighter\Formatter\CliFormatter([
    'string'      => ['color' => 'green'],
    'keyword'     => ['color' => 'yellow'],
    ...
])
```

#### HTML `\Kadet\Highlighter\Formatter\HtmlFormatter`
![HTML](http://kadet.net/keylighter/php-html.png)

Every token is placed inside it's own `span` so it can be easily styled with css.

```html
<span class="variable">$maxOption</span>
```

```css
pre > span.variable { color: #F7750D; }
```
#### Your own?
You can always write your own Formatter and use it for outputting data,
I will describe writing formatters on wiki soon™.


### Context sensitive
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
                new OpenRule(new RegexMatcher('/(<\w)/'), ['context' => ['!tag']]),
                new CloseRule(new SubStringMatcher('>'), ['priority' => -1, 'context' => ['!string']])
            ],

            'symbol.tag' => new Rule(new RegexMatcher('/<\\/?' . self::IDENTIFIER . '/', [
                'tag' => Token::NAME,
                'namespace' => '$.namespace'
            ]), ['context' => ['tag', '!string']]),

            'symbol.attribute' => new Rule(new RegexMatcher('/' . self::IDENTIFIER . '=/', [
                'tag' => Token::NAME,
                'namespace' => '$.namespace'
            ]), ['context' => ['tag', '!string']]),

            'string.single' => new Rule(new SubStringMatcher('\''), [
                'context' => ['tag'],
                'factory' => new TokenFactory('Kadet\\Highlighter\\Parser\\MarkerToken'),
            ]),

            'string.double' => new Rule(new SubStringMatcher('"'), [
                'context' => ['tag'],
                'factory' => new TokenFactory('Kadet\\Highlighter\\Parser\\MarkerToken'),
            ]),

            'tag.close' => new Rule(new RegexMatcher('/(<\/(?:\w+:)?(?:\w+)>)/')),
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

### Embedding languages
Many languages can be used simultaneously, *css* or *js* inside *html*, *sql* in  *php* and so on. **KeyLighter** can handle and highlight embedded languages without any problem.

![Optimized for php7.0](http://kadet.net/keylighter/language-embedding.png)

### Fast ![Optimized for php7.0](https://img.shields.io/badge/optimized%20for-PHP%207-8892BF.svg?style=flat-square)
Even though it wasn't supposed to be fastest code highlighter in PHP
it is still quite fast, up to few times faster than [GeSHi](http://geshi.org/).
It performs best on PHP 7 (more than 2x faster than GeSHi in every case).

You can find more about performance in [wiki](https://github.com/kadet1090/KeyLighter/wiki/Performance).

### Comes with CLI App
It's possible to install **KeyLighter** as global composer library
```bash
$ composer global require kadet/keylighter
```
Then you can use builtin simple cli highlighting app:
```bash
$ keylighter [options] file
Options:
        -l, --language       language    Source Language to highlight, default: html > php,
        -f, --format         format      Formatter used to highlight source, for example: html, default: cli,
        -h, --help                       This screen,
        -v, --verbose        level       Verbose mode,
        -lf, --formatters                List available formatters,
        -ll, --languages                 List available languages,
        -s, --silent                     No output.
```
If you want pipe into **KeyLighter** just specify `php://stdin` as file.

## Testing ![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/kadet1090/keylighter.svg?style=flat-square)
**KeyLighter** uses `phpunit` for testing:
```bash
$ phpunit
```

## Roadmap
There are still few things to do, you can find all of them (and even propose) on [trello](https://trello.com/b/9I4CO0Te/highlighter).

## Thanks
For [Maciej](https://github.com/ksiazkowicz), [Maciej](https://github.com/sobak) and Monika for all support, moral too.
