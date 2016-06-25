# Changelog
## 25.06.2016 [0.8.0]
### Added

 * `Kadet\Highlighter\Language\CommonFeatures` helper with common language features like string handling
 * `Kadet\Highlighter\Matcher\DelegateRegexMatcher` class
 * `Kadet\Highlighter\Parser\Rule::setMatcher` and `Kadet\Highlighter\Parser\Rule::getMatcher` methods
 * `Kadet\Highlighter\Parser\Context` class for storing context related information 
 * `Kadet\Highlighter\Parser\Result::getStart` method
 * `$offset` argument to `Kadet\Highlighter\Parser\UnprocessedTokens` methods
 * `Kadet\Highlighter\Language\Language::by*` factory methods
 * `Kadet\Highlighter\Language\Language::getAliases*` static method, for language metadata
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
