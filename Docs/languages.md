<!-- icon: tags -->
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

All alias definitions can be found in [`Config/aliases.php`](../Config/aliases.php)

## Language reference

<!-- aliasbegin -->
Class | Name | MIME | Filename
------|------|------|----------
`Kadet\Highlighter\Language\Php` | `php` | `text/x-php`, `application/x-php` | `*.php`, `*.phtml`, `*.inc`, `*.php?`
`Kadet\Highlighter\Language\Xml` | `xml`, `xaml` | `application/xml`, `text/xml` | `*.xml`, `*.xaml`
`Kadet\Highlighter\Language\Html` | `html` | `text/html` | `*.html`, `*.htm`
`Kadet\Highlighter\Language\PowerShell` | `powershell`, `posh` | `text/x-powershell`, `application/x-powershell` | `*.ps1`, `*.psm1`, `*.psd1`
`Kadet\Highlighter\Language\PlainText` | `plaintext`, `text`, `none` | `text/plain` | none
`Kadet\Highlighter\Language\Latex` | `tex`, `latex` | `application/x-tex`, `application/x-latex` | `*.tex`, `*.aux`, `*.toc`
`Kadet\Highlighter\Language\Ini` | `ini` | `text/x-ini`, `text/inf` | `*.ini`, `*.cfg`, `*.inf`
`Kadet\Highlighter\Language\JavaScript` | `js`, `jscript`, `javascript` | `application/javascript`, `application/x-javascript`, `text/x-javascript`, `text/javascript` | `*.js`, `*.jsx`
`Kadet\Highlighter\Language\Css` | `css` | `text/css` | `*.css`
`Kadet\Highlighter\Language\Css\Scss` | `scss` | `text/x-scss` | `*.scss`
`Kadet\Highlighter\Language\Css\Sass` | `sass` | `text/x-sass` | `*.sass`
`Kadet\Highlighter\Language\Css\Less` | `less` | `text/x-less` | `*.less`
`Kadet\Highlighter\Language\Sql` | `sql` | `text/x-sql` | `*.sql`
`Kadet\Highlighter\Language\Sql\MySql` | `mysql` | `text/x-mysql` | none
`Kadet\Highlighter\Language\Perl` | `perl` | `text/x-perl`, `application/x-perl` | `*.pl`, `*.pm`, `*.t`
`Kadet\Highlighter\Language\C` | `c` | `text/x-csrc`, `text/x-chdr` | `*.c`, `*.h`, `*.idc`
`Kadet\Highlighter\Language\Cpp` | `cpp`, `c++` | `text/x-c++src`, `text/x-c++hdr` | `*.cpp`, `*.hpp`, `*.hxx`, `*.cxx`, `*.cc`, `*.hh`
`Kadet\Highlighter\Language\CSharp` | `CSharp`, `C#` | `text/x-csharp` | `*.cs`
`Kadet\Highlighter\Language\Java` | `java` | `text/x-java` | `*.java`
`Kadet\Highlighter\Language\Python` | `python`, `py` | `text/x-python`, `application/x-python` | `*.py`
`Kadet\Highlighter\Language\Python\Django` | `django`, `jinja` | `application/x-django-templating`, `application/x-django-jinja` | none
`Kadet\Highlighter\Language\Markdown` | `markdown`, `md` | `text/markdown` | `*.markdown`, `*.md`
`Kadet\Highlighter\Language\Shell` | `shell`, `bash`, `zsh`, `sh` | `text/x-shellscript`, `application/x-shellscript` | `*.sh`, `*.zsh`, `*.bash`, `*.ebuild`, `*.eclass`, `*.exheres-0`, `*.exlib`, `.bashrc`, `bashrc`, `.bash_*`, `bash_*`, `PKGBUILD`
<!-- aliasend -->
