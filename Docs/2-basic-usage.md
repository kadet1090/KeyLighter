---
icon: lightbulb
iconType: regular
title: 2. usage
previous: 1-installation
next: 3-advanced-usage
---

# Usage

The simplest way of using KeyLighter is to use provided `highlight` function from `\Kadet\Highlighter` namespace.
This function takes 3 arguments: `$source` which is source to be highlighted, `$language` which describes used language and
`$formatter` which defines outputed format. As the default formatter is determined by runtime kind, we can omit `$formatter` argument,
and KeyLighter will pick proper one for us.

As we know, talk is cheap, so here is some code:

```php
use function \Kadet\Highlighter\highlight;
use \Kadet\Highlighter\Language;

$source    = file_get_contents(__FILE__); // contents of current file
$language  = new Language\Php();          // we know that this file is in PHP

echo highlight($source, $language);
```

We can also be explicit about used formatter like so:

```php
echo highlight($source, $language, new HtmlFormatter());
```

## Obtaining Language object

There are a lot situations where we can hardcode highlighted language, but there are probably many
more situations where we cannot do so. How we can obtain proper `Language` object then?

KeyLighter comes with 3 different ways of matching correct languages:
 - by language name (e.g. `php`) via `Language::byName($name)` will return `Language\Php`
 - by file extension (e.g. `file.py`) via `Language::byFilename($filename)` will return `Language\Python`
 - by mime type (e.g. `text/html`) via `Language::byMime($mime)` will return `Language\Html`

Let's assume that we are building some code snippet sharing site where user can choose language
from some kind of select input. In that situation we should use `Language::byName` function:

```php
// Note that we're using `\Kadet\Highlighter\Language\Language` class 
// instead of `\Kadet\Highlighter\Language` namespace.
use \Kadet\Highlighter\Language\Language; 
use function \Kadet\Highlighter\highlight;

// HTML formatter will do escaping for us.
echo highlight($_POST['source'], $Language::byName($_POST['language'])); 
```

We could also imagine, that user is uploading file to us via file input, so we have access
to filename and mime type, that we can take advantage of.

```php
use \Kadet\Highlighter\Language\Language; 
use function \Kadet\Highlighter\highlight;

// obtain source from temporary file
$source = file_get_contents($_FILES['form']['tmp_name']);

echo highlight($source, Language::byMime($_FILES['form']['type']));
// Or
echo highlight($source, Language::byFilename($_FILES['form']['name']));
```

## Embeddable languages

Some languages can be embedded in other languages, for example we often embed PHP in HTML
files, or JavaScript in HTML. 

There are two kinds of embedding - one where top-level language has knowledge about embedding - like in 
JavaScript example, and one where only embedded language knows where it begins and ends. The first kind 
should be handled automatically when using appropriate language (e.g. HTML), but in later we have to 
be explicit about embedding.

Let's say that we have file like that:
```html>php
<div><?php echo "oh look, it's php" ?></div>
```

Clearly embedding PHP into HTML file. We can achieve that by specifying embedded languages on Language 
object creation:

```php
$language = new Language\Html(['embedded' => [ new Language\Php ]]);
```

of course, it's not unique for HTML, we can embed every embeddable language into every other language and it will
work just fine - just remember, that order matters. We can even embed language into embedded language! 

```html>php>django
<html><?php echo "{% if x %}omg{% else %}it's twig{% endif %}" ?></html>
```

Here we have Twig (or jinja) embedded into PHP which itself is embedded in HTML.
```php
$language = new Language\Html([
    'embedded' => [ 
        new Language\Php(['embedded' => [ new Language\Twig ]]) 
    ]
]);
```

We also can embed multiple languages at once:
```php
$language = new Language\Html([
    'embedded' => [ 
        new Language\Php,
        new Language\Twig 
    ]
]);
```

But remember, that you should only embed languages which are desired to be embedded like PHP or Twig!

It's also possible to use `top-level > embedded` syntax in `byName` method:
```php
$language = Language::byName('html > php');
// has the same effect as
$language = new Language\Html(['embedded' => [ new Language\Php ]]);
```

but it's recommended to write your own logic on embedding languages if you need such feature.