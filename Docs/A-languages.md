---
icon: code
title: A. languages
---

# Languages
There are several ways of creating language objects, but we can divide
them into two basic categories:

## Directly with class name 
First of all you can just create new object, it's the best way if you exactly
know language that you want to highlight and it's not dependent on any input.
```php
use Kadet\Highlighter\Language;

$language = new Language\Php([options]); 
```

## Indirectly
But it's not the only way, if chosen language is dependent on some 
kind of input, it'd be much better to use some of helper factory methods:

```php
use Kadet\Highlighter\Language\Language;

$language = Language::byName('php', [options]); // with name
$language = Language::byMime('application/x-php', [options]); // with MIME type
$language = Language::byFilename('test.php', [options]); // by extension
```

All alias definitions can be found in [`Config/metadata.php`](../Config/metadata.php)
> **NOTE**: This is computer generated file

## Language reference

<!-- aliasbegin -->
Class | Name | MIME | Extension
------|------|------|----------
`Kadet\Highlighter\Language\Apache` | `apache` | `application/xml`, `text/xml` | `.htaccess`
`Kadet\Highlighter\Language\Assembler` | `asm`, `assembler` | `text/x-asm` | `*.asm`
`Kadet\Highlighter\Language\C` | `c` | `text/x-csrc`, `text/x-chdr` | `*.c`, `*.h`, `*.idc`
`Kadet\Highlighter\Language\CSharp` | `CSharp`, `C#` | `text/x-csharp` | `*.cs`
`Kadet\Highlighter\Language\Cobol` | `cobol` | `text/x-cobol` | `*.cbl`
`Kadet\Highlighter\Language\Cpp` | `cpp`, `c++` | `text/x-c++src`, `text/x-c++hdr` | `*.cpp`, `*.hpp`, `*.hxx`, `*.cxx`, `*.cc`, `*.hh`
`Kadet\Highlighter\Language\Css` | `css` | `text/css` | `*.css`
`Kadet\Highlighter\Language\Css\Less` | `less` | `text/x-less` | `*.less`
`Kadet\Highlighter\Language\Css\Sass` | `sass` | `text/x-sass` | `*.sass`
`Kadet\Highlighter\Language\Css\Scss` | `scss` | `text/x-scss` | `*.scss`
`Kadet\Highlighter\Language\Go` | `go`, `golang` | `text/x-go`, `application/x-go`, `text/x-golang`, `application/x-golang` | `*.go`
`Kadet\Highlighter\Language\Haskell` | `haskell` | `text/x-haskell` | `*.hs`
`Kadet\Highlighter\Language\Html` | `html` | `text/html` | `*.html`, `*.htm`
`Kadet\Highlighter\Language\Http` | `http` | none | none
`Kadet\Highlighter\Language\Ini` | `ini` | `text/x-ini`, `text/inf` | `*.ini`, `*.cfg`, `*.inf`
`Kadet\Highlighter\Language\Java` | `java` | `text/x-java` | `*.java`
`Kadet\Highlighter\Language\JavaScript` | `js`, `jscript`, `javascript`, `json` | `application/javascript`, `application/x-javascript`, `text/x-javascript`, `text/javascript`, `application/json` | `*.js`, `*.jsx`, `*.json`
`Kadet\Highlighter\Language\Latex` | `tex`, `latex` | `application/x-tex`, `application/x-latex` | `*.tex`, `*.aux`, `*.toc`
`Kadet\Highlighter\Language\Markdown` | `markdown`, `md` | `text/markdown` | `*.markdown`, `*.md`
`Kadet\Highlighter\Language\Perl` | `perl` | `text/x-perl`, `application/x-perl` | `*.pl`, `*.pm`, `*.t`
`Kadet\Highlighter\Language\Php` | `php` | `text/x-php`, `application/x-php` | `*.php`, `*.phtml`, `*.inc`, `*.php?`
`Kadet\Highlighter\Language\PlainText` | `plaintext`, `text`, `none` | `text/plain` | none
`Kadet\Highlighter\Language\PowerShell` | `powershell`, `posh` | `text/x-powershell`, `application/x-powershell` | `*.ps1`, `*.psm1`, `*.psd1`
`Kadet\Highlighter\Language\Prolog` | `prolog` | `text/x-prolog` | `*.prolog`
`Kadet\Highlighter\Language\Python` | `python`, `py` | `text/x-python`, `application/x-python` | `*.py`
`Kadet\Highlighter\Language\Python\Django` | `django`, `jinja` | `application/x-django-templating`, `application/x-jinja` | none
`Kadet\Highlighter\Language\Ruby` | `ruby` | `text/x-ruby`, `application/x-ruby` | `*.rb`, `*.rbw`, `Rakefile`, `*.rake`, `*.gemspec`, `*.rbx`, `*.duby`, `Gemfile`
`Kadet\Highlighter\Language\Shell` | `shell`, `bash`, `zsh`, `sh` | `text/x-shellscript`, `application/x-shellscript` | `*.sh`, `*.zsh`, `*.bash`, `*.ebuild`, `*.eclass`, `*.exheres-0`, `*.exlib`, `.bashrc`, `bashrc`, `.bash_*`, `bash_*`, `PKGBUILD`
`Kadet\Highlighter\Language\Sql` | `sql` | `text/x-sql` | `*.sql`
`Kadet\Highlighter\Language\Sql\MySql` | `mysql` | `text/x-mysql` | none
`Kadet\Highlighter\Language\Twig` | `twig` | `text/x-twig` | `*.twig`
`Kadet\Highlighter\Language\TypeScript` | `ts`, `typescript` | `application/typescript`, `application/x-typescript`, `text/x-typescript`, `text/typescript` | `*.ts`, `*.tsx`
`Kadet\Highlighter\Language\UnifiedDiff` | `diff`, `patch` | `text/x-diff`, `text/x-patch`, `application/x-patch`, `application/x-diff` | `*.patch`, `*.diff`
`Kadet\Highlighter\Language\Xaml` | `xaml` | none | `*.xaml`
`Kadet\Highlighter\Language\Xml` | `xml` | none | `*.xml`
<!-- aliasend -->
