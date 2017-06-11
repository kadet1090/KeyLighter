# LaTeX Formatter

![The default theme](https://user-images.githubusercontent.com/2938672/27013405-725720aa-4ee3-11e7-808f-b1c7ebe7fda3.png)

KeyLighter supports outputting LaTeX, however some setup is required for this
to work properly:

- Unless you use XeLaTeX or LuaLaTeX, `\usepackage[T1]{fontenc}` is required;
- If you apply a theme which uses colors, `\usepackage{xcolor}` is also mandatory;
- If you are using Latin Modern, `lighttt` option is recommended for better
  distinction between normal and bold text.
- The code should be inside `alltt` environment or similar (but not `verbatim`)
  for correct line breaks.
- `\usepackage{upquote}` is recommended to get straight-up quotes.

## Theming

The default theme (utilizing only font attributes) is in `Styles/Latex/Default.php`.
Tokens may be styled with bold/italic font variant, underline and of course colors.
Available colors are determined by what options are given to `xcolor`.
