# Changelog
## 0.8 unreleased
### Engine
 * Processing is now handled with `Kadet\Highlighter\Parser\Token\Token::process` method
 * Removed ` Kadet\Highlighter\Parser\GreedyParser`
 * Context is now placed in its own class `Kadet\Highlighter\Parser\Context`
 * `Kadet\Highlighter\Parser\Result` now stores reference to first token
 * Added `Kadet\Highlighter\Language\CommonFeatures` helper with common language features like string handling
 * Added `Kadet\Highlighter\Matcher\DelegateRegexMatcher`
 * `Kadet\Highlighter\Parser\Token\Token` simplified
   * Removed `start` and `end` options from constructor, now they are handled by factory
   * Removed redundant `$index` property
 * Added `Kadet\Highlighter\Parser\Rule::setMatcher` and `Kadet\Highlighter\Parser\Rule::setMatcher` methods
 * About 10-15% performance improvement
 * Added more tests

### Languages
 * Added `C` highlighting
 * Added `Python` highlighting
  * Added `Jinja/Django` templates highlighting
 * Added `Markdown`
 * Added `C++` highlighting
 * Added `C#` highlighting
 * Added `Java` highlighting
 * `XML` and `HTML` now highlights entities
 * `CSS` highlights web colors
 * Fixed `SCSS` tag highlighting
