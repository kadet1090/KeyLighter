# Changelog
## Unreleased [0.8.0]
### Added

 * `Kadet\Highlighter\Language\CommonFeatures` helper with common language features like string handling
 * `Kadet\Highlighter\Matcher\DelegateRegexMatcher` class that is able to 
 * `Kadet\Highlighter\Parser\Rule::setMatcher` and `Kadet\Highlighter\Parser\Rule::setMatcher` methods
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
 
 
### Changed
 * `Kadet\Highlighter\Parser\Result::__constructor($source, $tokens)` -> `Kadet\Highlighter\Parser\Result::__constructor($source, Token $start)`
 * Processing is now handled with `Kadet\Highlighter\Parser\Token\Token::process` method
 * `Kadet\Highlighter\Parser\Token\Token` simplified
 
* * * 
 * **XML** and **HTML** now highlights entities
 * **CSS** highlights web colors
 * Many other fixes

### Removed
 * `Kadet\Highlighter\Parser\GreedyParser` in favor of `Kadet\Highlighter\Parser\Token\Token::process`
 * Redundant `Kadet\Highlighter\Parser\Token\Token::$index` property
 * `start` and `end` from `Kadet\Highlighter\Parser\Token\Token`s constructor `$option`s, now they are handled by factory

### Other
 * More tests
 * About 10-15% performance improvement
