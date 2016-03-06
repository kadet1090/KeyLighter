![Logo](http://kadet.net/keylighter/logo.png)
# KeyLighter [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kadet1090/KeyLighter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kadet1090/KeyLighter/?branch=master)

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

### Simple to use
Just like any other composer library add `kadet/keylighter` to your 
`composer.json` require section, include autoload file and you're ready to go.

Simple usage example:
```php
echo \Kadet\Highlighter\Highlighter::highlight($source, $language, $formatter); 
```
Thats it, nothing more.

Where:
`$language` is language, you can provide object of `\Kadet\Highlighter\Language\Language` type or registered alias (i.e. "xml"). It is even possible to embed one language in other: "xml > php" means php embedded in xml file. 

`$formatter` is implementation of `\Kadet\Highlighter\Formatter\FormatterInterface`.

### It works on CLI! And more!
KeyLighter was originally designed as CLI highlighter for my own usage, 
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

### Nesting languages (PARTIALLY DONE)
Many languages can be injected inside others, for example CSS or JS in HTML, It should be supported. 
Foundaments for that are done, it's possible to specify start and end tokens for languages, 
but not possible to parse many languages at once.

## Thanks
For [Maciej](https://github.com/ksiazkowicz), [Maciej](https://github.com/sobak) and Monika for all support, moral too.