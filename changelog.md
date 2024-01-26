# Changelog
## Unreleased [0.10.0]

### Added
### Fixed

## 2024-01-26 [0.9.0]
### Added
 * Languages can be marked as `standalone` and `injectable` in `getMetadata` static method
 * HTML Formatters now support tag and class prefix configuration via constructor `$options` parameter
 * `_utils.scss` file with few utilities useful for style writing, like prefix support etc.
 * `Rules` container now supports named rules
 * `Kadet\Highlighter\Parser\Rule` setters are now fluent
 * `Kadet\Highlighter\Matcher\WordMatcher` now has option to merge. subtract and retrieve options and words.
 * `Kadet\Highlighter\Formatter\AbstractFormatter` helper for creating formatters
 * You can now enable line-by-line mode in formatters via `['lines' => ['enabled' => true]]` option.
 * `*.json` and `json` alias for highlighting json files using `javascript` highlihgter
 
 ***
 
 * **XAML** highlighting via `Kadet\Highlighter\Language\Xaml` instead of using **XML**
 * **Shell** languages now have better variable and flag matching
 * **Twig** highlighting via `Kadet\Highlighter\Language\Twig` based on django/jinja 
 * **C** and **C++** now highlights code in macros
 * **Haskell** highlighting via `Kadet\Highlighter\Language\Haskell`
 * **Apache conf** highlighting via `Kadet\Highlighter\Language\Apache`
 * **Typescript** highlighting via `Kadet\Highlighter\Language\Typescript`
 * **Prolog** highlighting via `Kadet\Highlighter\Language\Prolog`
 * **Assembly** highlighting via `Kadet\Highlighter\Language\Assembler`
 * **Cobol** highlighting via `Kadet\Highlighter\Language\Cobol`
 * **Diff** highlighting via `Kadet\Highlighter\Language\UnifiedDiff`
 * **PHP** now supports features from PHP 8.x

## Changed
 * **BC BREAK** KeyLighter requires PHP 7.3+ now (instead of PHP 5.5 as previously)
 * **BC BREAK** `LaTexFormatter` and `CliFormatter` no longer have `$styles` in constructor, now you should set styles v
 via option `styles` `['styles' => [...]]`
 * renamed `Kadet\Highlighter\Language\Language::getAliases` to `getMetadata` as it now stores not only aliases

### Fixed
 * **PHP** now correctly matches multi-line comments
 * **XML** now correctly matches closing tags with `-`
 * **Java** fix #4 - class names in declarations and instantiations are now highlighted correctly
 * **Shell** highlighting now matches parameters and paths with `do` correctly
 * **JavaScript** and **TypeScript** now correctly recognize JSDoc comments as docblock tokens
 * **JavaScript** and **TypeScript** now correctly highlight annotations inside JSDoc comments

## 2018-02-08 [0.8.3]
### Added
 * **Java** Now matches class names

### Fixed
 * **Java** now correctly matches its primitive types
 * Fixed PHP 7.2 deprecation
 
## 2016-09-16 [0.8.2]
### Fixed
 * Fixed #2 - `symfony/console` is now suggested dependency and required only in dev.

## 2016-07-07 [0.8.1]
### Fixed
 * Matching **XML** tags with `-` char
 * `parent > injected` language syntax in CLI application

## 2016-06-25 [0.8.0]
### Added

 * `Kadet\Highlighter\Language\CommonFeatures` helper with common language features like string handling
 * `Kadet\Highlighter\Matcher\DelegateRegexMatcher` class
 * `Kadet\Highlighter\Parser\Rule::setMatcher` and `Kadet\Highlighter\Parser\Rule::getMatcher` methods
 * `Kadet\Highlighter\Parser\Context` class for storing context related information 
 * `Kadet\Highlighter\Parser\Result::getStart` method
 * `$offset` argument to `Kadet\Highlighter\Parser\UnprocessedTokens` methods
 * `Kadet\Highlighter\Language\Language::by*` factory methods
 * `Kadet\Highlighter\Language\Language::getAliases` static method, for language metadata
 * `Kadet\Highlighter\Utils\Singleton::init` singletons constructor
 * `dev:generate-table` and `dev:aliases` commands for cli application
 
* * * 
 * **C** highlighting via `Kadet\Highlighter\Language\C`
 * **Python** highlighting via `Kadet\Highlighter\Language\Python`
 * **Jinja/Django** templates highlighting via `Kadet\Highlighter\Language\Python\Django`
 * **Markdown** highlighting via `Kadet\Highlighter\Language\Markdown`
 * **C++** highlighting via `Kadet\Highlighter\Language\Cpp`
 * **C#** highlighting via `Kadet\Highlighter\Language\CSharp`
 * **Java** highlighting via `Kadet\Highlighter\Language\Java`
 * **shell**/**bash**/**zsh** highlighting via `Kadet\Highlighter\Language\Shell`
 * **Go** highlighting via `Kadet\Highlighter\Language\Go`
 * **Ruby** highlighting via `Kadet\Highlighter\Language\Ruby`
 * **XML** and **HTML** now highlights entities
 * **CSS** highlights web colors
 * **PHP** and few others, matches in-string expressions
 
### Changed
 * `Kadet\Highlighter\Parser\Result::__constructor($source, $tokens)` -> `Kadet\Highlighter\Parser\Result::__constructor($source, Token $start)`
 * Processing is now handled with `Kadet\Highlighter\Parser\Token\Token::process` method
 * `Kadet\Highlighter\Parser\Token\Token` simplified
 * `parameter` token renamed to `symbol.parameter`
 * `annotation` token renamed to `symbol.annotation`
 * `keyword.escape` token renamed to `operator.escape`
 * For semantic reasons `Kadet\Highlighter\Formatter\DebugFormatter` now extends `Kadet\Highlighter\Formatter\CliFormatter`
 * CLI formatting styles now accept callables
 * Token offsetting is now handed by containers rather than factory
 * Completely rewritten console application - Now it's based on `symfony/console`
 * Console application now determines language based on filename
 
### Fixed
 * **PHP** now correctly matches multiple implemented interfaces
 * **PHP** now correctly matches variables as first token
 * **PHP** now correctly matches types in doc comments
 * **PHP** now correctly handles escape sequences in single quoted strings (see #1)
 * Languages injected inside injected languages are now handled correctly

### Removed
 * `Kadet\Highlighter\Parser\GreedyParser` in favor of `Kadet\Highlighter\Parser\Token\Token::process`
 * Redundant `Kadet\Highlighter\Parser\Token\Token::$index` property
 * `start` and `end` from `Kadet\Highlighter\Parser\Token\Token`s constructor `$option`s, now they are handled by factory
 * `Kadet\Highlighter\Parser\TokenFactory::getOffset` and `setOffset` methods

### Other
 * More tests
 * About 10-15% performance improvement
