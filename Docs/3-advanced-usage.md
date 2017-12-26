---
icon: search
iconType: regular
title: 3. advanced usage
previous: 2-basic-usage
next: 3-advanced-usage
---

# Advanced Usage

So now you know how to install and use KeyLighter in most scenarios, but what if you need
to have little bit more control on language matching process? 

In that situation, `\Kadet\Highlighter\KeyLighter` class comes with help. It basically acts like 
language and formatter provider. By default there exist global instance (accessed with `KeyLighter::get()`) which is used 
internally in all `Language::by*` methods, and has a lot of predefined rules. But you can also create provider with your own rules!

```php
$keylighter = \Kadet\Highlighter\KeyLighter::get(); // global instance
$keylighter = new \Kadet\Highlighter\KeyLighter(); // your own
```

> **NOTE:** `Language::by*` factory methods covered in [languages] section will always refer to global object!

To add new language into registry, you just have to call `register` method:
```php
$keylighter->register(
    $class, [
        'name'      => [...],
        'mime'      => [...],
        'extension' => [...]
    ]
);
```

If you need to have some more control over creating process and class name is simply not enough,
it's possible to provide [anonymous function] that will act like factory:
```php
$keylighter->register(
    function($options) {
        ...
        return $language;
    }, [
        'name'      => [...],
        'mime'      => [...],
        'extension' => [...]
    ]
);
```
> **NOTE:** that function will be called on every match!

It's perfect way of registering embedded languages, like PHP in HTML:
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

You can also match languages just like `Language::by*` functions:
```php
$keylighter->languageByName($name, $options = []);
$keylighter->languageByMime($mime, $options = []);
$keylighter->languageByExt($filename, $options = []);
```

It's also possible to enumerate all registered languages:
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

[languages]: ./A-languages