---
icon: download
iconType: regular
title: 1. installation
next: 2-basic-usage
---

# Installation as library

First off, you need to have at least PHP 5.5, and [composer] installed, and then you can install
KeyLighter into your project by simply executing:

```bash
$ composer require kadet/keylighter
```

And include composers autoloader somewhere in your code:
```php
require 'vendor/autoload.php'
```

And that's it. Technically it is possible to use KeyLighter without composer,
but then you'd have to setup your own auto loading that is compatible with [PSR-4]
which I don't recommend.

## Using KeyLighter as CLI tool

It's also possible to use KeyLighter as CLI tool for displaying highlighted code right in your console window.
The easiest way of doing so is to download [keylighter.phar] file from this site, and then you can use it like so:

```bash
$ php keylighter.phar [highlight] [-l|--language [LANGUAGE]] [-f|--format [FORMAT]] [--] <file> <file2>...
```

[composer]: https://getcomposer.org/
[PSR-4]: http://www.php-fig.org/psr/psr-4/
[keylighter.phar]: http://keylighter.kadet.net/download/keylighter.phar