---
icon: lightbulb-o
---

# Usage ![stability-stable](https://img.shields.io/badge/stability-stable-green.svg?style=flat-square)

**KeyLighter** is really simple to use in any kind of project. All you 
need is simple to use `\Kadet\Highlighter\highlight` function, 
which basically is just a simple wrapper for `\Kadet\Highlighter\KeyLighter::highlight` method:

```php
function highlight($source, Language $language, FormatterInterface $formatter = null)
{
    return KeyLighter::get()->highlight($source, $language, $formatter);
}
```

There are several ways of obtaining Language object, you can find them all
in [this document](./languages). There are some real life examples:

```php
use \Kadet\Highlighter; // Or use function \Kadet\Highlighter\highlight; as of PHP 5.6.
use \Kadet\Highlighter\Language;

$source = file_get_contents(__FILE__);

echo Highlighter\highlight(file_get_contents(__FILE__), new Language\Php());
```

Highlighting source uploaded via form:
```php
echo Highlighter\highlight($_POST['source'], Language::byName($_POST['language']));
```

Highlighting uploaded file:
```php
$source = file_get_contents($_FILES['form']['tmp_name']);

echo Highlighter\highlight($source, Language::byMime($_FILES['form']['type']));
// Or
echo Highlighter\highlight($source, Language::byFilename($_FILES['form']['name']));
```

## `\Kadet\Highlighter\KeyLighter`

`\Kadet\Highlighter\KeyLighter` acts as Language provider,
you can either use global (accessed with `\Kadet\Highlighter\KeyLighter::get()` method) instance
which has many predefined aliases, mime types and extensions for referencing various
languages or create your own.

```php
$keylighter = \Kadet\Highlighter\KeyLighter::get(); // global instance
$keylighter = new \Kadet\Highlighter\KeyLighter(); // your own
```

> **NOTE:** `Language::by*` factory methods covered by [this document](./languages.mdme) will always refer to global object!

You can easily add your own aliases etc. for every language with these methods:
```php
$keylighter->register(
    $class, [
        'name'      => [...],
        'mime'      => [...],
        'extension' => [...]
    ]
);
```

```php
$keylighter->register(
    function($options) {
        ...
    }, [
        'name'      => [...],
        'mime'      => [...],
        'extension' => [...]
    ]
);
```

In second example closure will be called every time when referencing that language, 
it's perfect way to register some embedded languages: 
```php
$keylighter->register(
    function($options) {
        return new \Kadet\Highlighter\Language\Html(array_merge_recursive([
            'embedded' => [new \Kadet\Highlighter\Language\Php]
        ], $options));
    }, [
        'name'      => ['phtml'],
        'mime'      => ['text/php+html'],
        'extension' => ['*.phtml']
    ]
);
```

You can reference registered languages with:
```php
$keylighter->languageByName($name, $options = []);
$keylighter->languageByMime($mime, $options = []);
$keylighter->languageByExt($filename, $options = []);
```

To obtain list of registered names/mime types/extensions use:
```php
$keylighter->registeredLanguages('name'); // All registered names
// Result
[
    'php'  => '\Kadet\Highlighter\Language\Php',
    'html' => '\Kadet\Highlighter\Language\Html',
    ...
]
```
```php
$keylighter->registeredLanguages('mime'); // All registered mime types
// Result:
[
    'text/x-php' => '\Kadet\Highlighter\Language\Php',
    'text/html'  => '\Kadet\Highlighter\Language\Html',
    ...
]
```
```php
$keylighter->registeredLanguages('extension'); // All registered extensions
// Result:
[
    '*.php'  => '\Kadet\Highlighter\Language\Php',
    '*.html' => '\Kadet\Highlighter\Language\Html',
    ...
]
```
