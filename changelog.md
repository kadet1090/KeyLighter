# Changelog
## Unreleased [0.8.0]
### Added

 * `Kadet\Highlighter\Language\CommonFeatures` helper with common language features like string handling
 * `Kadet\Highlighter\Matcher\DelegateRegexMatcher` class
 * `Kadet\Highlighter\Parser\Rule::setMatcher` and `Kadet\Highlighter\Parser\Rule::getMatcher` methods
 * `Kadet\Highlighter\Parser\Context` class for storing context related information 
 * `Kadet\Highlighter\Parser\Result::getStart` method
 
* * * 
 * **C** highlighting via `Kadet\Highlighter\Language\C`
 * **Python** highlighting via `Kadet\Highlighter\Language\Python`
 * **Jinja/Django** templates highlighting via `Kadet\Highlighter\Language\Python\Django`
 * **Markdown** highlighting via `Kadet\Highlighter\Language\Markdown`
 * **C++** highlighting via `Kadet\Highlighter\Language\Cpp`
 * **C#** highlighting via `Kadet\Highlighter\Language\CSharp`
 * **Java** highlighting via `Kadet\Highlighter\Language\Java`
 * **shell**/**bash**/**zsh** highlighting via `Kadet\Highlighter\Language\Shell`
 * **XML** and **HTML** now highlights entities
 * **CSS** highlights web colors
 * **PHP** Now matches in string expressions
 * `Kadet\Highlighter\Language\Language::by*` factory methods
 * `Kadet\Highlighter\Utils\Singleton::init` singletons constructor
 
### Changed
 * `Kadet\Highlighter\Parser\Result::__constructor($source, $tokens)` -> `Kadet\Highlighter\Parser\Result::__constructor($source, Token $start)`
 * Processing is now handled with `Kadet\Highlighter\Parser\Token\Token::process` method
 * `Kadet\Highlighter\Parser\Token\Token` simplified
 * `parameter` token renamed to `symbol.parameter`
 * `annotation` token renamed to `symbol.annotation`
 * `keyword.escape` token renamed to `operator.escape`
 * For semantic reasons `Kadet\Highlighter\Formatter\DebugFormatter` now extends `Kadet\Highlighter\Formatter\CliFormatter`
 * CLI formatting styles now accept callables
 * Many fixes
 
### Fixed
 * **PHP** now correctly matches multiple implemented interfaces
 * **PHP** now correctly matches variables as first token

### Removed
 * `Kadet\Highlighter\Parser\GreedyParser` in favor of `Kadet\Highlighter\Parser\Token\Token::process`
 * Redundant `Kadet\Highlighter\Parser\Token\Token::$index` property
 * `start` and `end` from `Kadet\Highlighter\Parser\Token\Token`s constructor `$option`s, now they are handled by factory

### Other
 * More tests
 * About 10-15% performance improvement
