---
icon: file-pdf
title: LaTeX
---

# LaTeX Formatter

```php
    $formatter = new \Kadet\Highlighter\Formatter\LatexFormatter($styles = false);
```

![The default theme](https://user-images.githubusercontent.com/2938672/27013405-725720aa-4ee3-11e7-808f-b1c7ebe7fda3.png)

## Setup

This formatter does output only highlighted part - not full LaTeX document - and because so it requires 
some additional setup to compile properly:

 * Unless you use XeLaTeX or LuaLaTeX, `\usepackage[T1]{fontenc}` is required;
 * If you apply a theme which uses colors, `\usepackage{xcolor}` is also mandatory;
 * If you are using Latin Modern, `lighttt` option is recommended for better
  distinction between normal and bold text.
 * The code should be inside `alltt` environment or similar (but **not** `verbatim`)
  for correct line breaks.
 * `\usepackage{upquote}` is recommended to get straight-up quotes.

Example correct preamble:
```tex
\documentclass[]{article}

\usepackage[T1]{fontenc}
\usepackage[lighttt]{lmodern}
\usepackage{alltt}
\usepackage{upquote}

\begin{document}
\begin{alltt}

Generated code goes here...

\end{alltt}
\end{document}
```

## Theming

Theme file is built just like [CLI theme file]: 

```php
<?php return [
    'token.name' => [ ...options... ]  
];
```

The default theme (utilizing only font attributes) can be found in [`Styles/Latex/Default.php`].
Tokens may be styled with bold/italic font variant, underline and of course colors.
Available colors are determined by what options are given to `xcolor`.

[CLI theme file]: ./cli
[`Styles/Latex/Default.php`]: https://github.com/kadet1090/KeyLighter/blob/master/Styles/Latex/Default.php