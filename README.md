![Logo](https://keylighter.kadet.net/img/logo.png)

[![Packagist](https://img.shields.io/packagist/v/kadet/keylighter.svg?style=flat-square)](https://packagist.org/packages/kadet/keylighter)
[![Try it](https://img.shields.io/badge/www-try%20it-FF9700.svg?style=flat-square)](https://keylighter.kadet.net)

[![stability: stable](https://img.shields.io/badge/Public%20API-stable-green.svg?style=flat-square)](Docs/2-basic-usage.md)
[![stability: unstable](https://img.shields.io/badge/Internal%20API-unstable-yellow.svg?style=flat-square)](Docs/3-advanced-usage.md)

Yet another Syntax Highlighter in PHP meant to be as extensible and easy to use 
as it only can, but with performance in mind.

You can try it live with the most recent version on https://keylighter.kadet.net/.

## Name
Name "KeyLighter" is inspired by Key Light concept in photography and 
cinematography.

> The key light is the first and usually most important light a photographer, 
> cinematographer, lighting cameraman, or other scene composer will use in a 
> lighting setup. The purpose of the key light is to highlight the form and 
> dimension of the subject.

KeyLighter is supposed to do the same thing - for code.

# Installation
```bash
$ composer require kadet/keylighter
```

To use **KeyLighter** you just need PHP 7.1.3 or later, no special extensions 
required.

## Global installation
It's possible to install **KeyLighter** as a global composer library
```bash
$ composer global require kadet/keylighter
```
Then you can use builtin simple cli highlighting app:
```bash
$ keylighter [command = highlight] [-l|--language [LANGUAGE]] [-f|--format [FORMAT]] [-d|--debug [DEBUG]] [--]  <path>...
```
If you want pipe into **KeyLighter** just specify `php://stdin` as path. You can 
use `list` command to see all available commands, and `--help` argument for 
detailed help. You don't have to specify `highlight` command explicitly. 

### PowerShell
You're using PowerShell on Windows? Cool! **KeyLighter** comes with integrated 
PowerShell module that makes CLI usage even better. Just import module (For 
example in profile), and you're ready to go.

```powershell
PS> Import-Module "${env:APPDATA}\Composer\vendor\kadet\keylighter\bin\KeyLighter.psd1"
```

To use autocompletion features you will need PowerShell v5 (Comes with windows 
10) or install [TabExpansion++](https://github.com/lzybkr/TabExpansionPlusPlus) 
Module.

![Powershell Support](https://i.imgur.com/jH2VRA8.gif)

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

You can find all available languages [here](https://github.com/kadet1090/KeyLighter/tree/master/Language) 
and formatters [here](https://github.com/kadet1090/KeyLighter/tree/master/Formatter).

### It works on CLI! And more!
**KeyLighter** was originally designed as CLI highlighter for my own usage,
but then I decided that it should be able to generate any possible output,
currently supported:

#### Cli `\Kadet\Highlighter\Formatter\CliFormatter`
![CLI](https://i.imgur.com/b2bMVrw.png)

It can even be styled, default styles are stored in `Styles\Cli\Default.php`, 
but you can just pass additional argument into a constructor:

```php
new \Kadet\Highlighter\Formatter\CliFormatter([
    'string'      => ['color' => 'green'],
    'keyword'     => ['color' => 'yellow'],
    ...
])
```

#### HTML `\Kadet\Highlighter\Formatter\HtmlFormatter`
![HTML](https://i.imgur.com/BRThsX2.png)

Every token is placed inside it's own `span` and classes are prefixed so it can 
be easily styled with css and should not interfere with any of your existing 
classes

```html
<span class="kl-variable">$maxOption</span>
```

```css
pre > span.kl-variable { color: #F7750D; }
```
#### Your own?
It's easy to write your own formatters. Documentation coming soon.

### Context sensitive
Some tokens are valid in some contexts, some are not. This library is context 
sensitive, and you can define when they are valid.

In this case, context mean just "inside of other token", for example lets assume 
that `string` token is defined as everything from " to the next " and `keyword` 
is defined as 'sit' substring.

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

So as you can see `keyword` is inside of `string`, and therefore is not valid 
and should be deleted. You can define tokens valid only in some context, or 
invalid in other.

Oh, and token names cascade, it means that `string.single` is `string`, but 
`string` is necessarily not `string.single`.

### Write your own language definitions easily
It's possible to easily extend `KeyLighter` with new languages, more detailed 
documentation coming soon.

For example XML definition looks like this:
```php
class Xml extends GreedyLanguage
{
    private const IDENTIFIER = '(?P<namespace>[\w\.-]+:)?(?P<name>[\w\.-]+)';

    /**
     * Tokenization rules
     */
    public function setupRules()
    {
        $this->rules->addMany([
            'tag.open'  => [
                new OpenRule(new RegexMatcher('/(<[\w\.-]+)[:\/>:\s]/')),
                new CloseRule(new SubStringMatcher('>'), ['context' => ['!string', '!comment']])
            ],
            'tag.close' => new Rule(new RegexMatcher('/(<\/' . self::IDENTIFIER . '>)/')),

            'symbol.tag' => new Rule(new RegexMatcher('/<\\/?' . self::IDENTIFIER . '/', [
                'name'      => Token::NAME,
                'namespace' => '$.namespace'
            ]), ['context' => ['tag', '!string']]),

            'symbol.attribute' => new Rule(new RegexMatcher('/' . self::IDENTIFIER . '=/', [
                'name'      => Token::NAME,
                'namespace' => '$.namespace'
            ]), ['context' => ['tag', '!string']]),

            'constant.entity' => new Rule(new RegexMatcher('/(&(?:\#\d+|[a-z])+;)/si')),

            'comment' => new Rule(new CommentMatcher(null, [['<!--', '-->']])),
            'string'  => CommonFeatures::strings(['single' => '\'', 'double' => '"'], ['context' => ['tag']]),
        ]);
    }

    /** {@inheritdoc} */
    public function getIdentifier()
    {
        return 'xml';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['xml'],
            'mime'      => ['application/xml', 'text/xml'],
            'extension' => ['*.xml']
        ];
    }
}
```

I will try to write as many definitions as I only can, but any PRs are welcome.

### Embedding languages
Many languages can be used simultaneously, *css* or *js* inside *html*, *sql* in  
*php* and so on. **KeyLighter** can handle and highlight embedded languages 
without any problem.

![Embedded languages](https://i.imgur.com/gJr6shy.png)

### Fast
Even though it wasn't supposed to be fastest code highlighter in PHP it is still 
quite fast, more than 2x faster than [GeSHi](https://geshi.org/).

## Testing
**KeyLighter** uses `phpunit` for testing:
```bash
$ ./vendor/bin/phpunit
```

## Roadmap
There are still few things to do, you can find all on [trello](https://trello.com/b/9I4CO0Te/highlighter).

## Contributing
See [CONTRIBUTING.md](./CONTRIBUTING.md) for details.

## Thanks
For [Maciej](https://github.com/ksiazkowicz), [Maciej](https://github.com/sobak) 
and Monika for all support, moral too.

