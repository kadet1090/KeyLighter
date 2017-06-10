---
icon: tags
---

Token Types
===========

There are several standard token types available for use and styling.
In addition every type can have subtypes, for example we can have single quoted and double quoted string - both are 
strings, but different. So we can define `string.single` and `string.double` both are `string`s but `string.single` 
is not `string.double`. 

## Standard token types:
Token type                      | Description                                     | Example
-------------------------------:|-------------------------------------------------|----------
`string`                        | String/Char literals                            | <code class="keylighter inline"><span class="string single">'single quoted'</span></code> <code class="keylighter inline"><span class="string double">"double quoted"</span></code>
`number`                        | Number literals integers, doubles, floats etc.  | <code class="keylighter inline"><span class="number">10</span></code> <code class="keylighter inline"><span class="number">.15</span></code> <code class="keylighter inline"><span class="number">10.5e10</span></code>
`symbol`                        | Code symbols - class names, function names etc. | <code class="keylighter inline"><span class="symbol">Symbol</span></code>
`symbol.class`                  | Class Name                                      | <code class="keylighter inline"><span class="symbol class">SomeClass</span></code>
`symbol.class.interface`        | Interface Name                                  | <code class="keylighter inline"><span class="symbol class interface">SomeInterface</span></code>
`symbol.function`               | Function definition (**NOT CALL!**)             | <code class="keylighter inline"><span class="symbol function">FizzBuzz</span></code>
`symbol.annotation`             | Annotation                                      | <code class="keylighter inline"><span class="symbol annotation">@annotation</span></code> <code class="keylighter inline">[<span class="symbol annotation">annotation</span>]</code>
`constant`                      | Constants defined in language                   | <code class="keylighter inline"><span class="constant">true</span></code> <code class="keylighter inline"><span class="constant">IS_DEBUG</span></code>
`variable`                      | Variables, including sigils (like `$` in PHP)   | <code class="keylighter inline"><span class="variable">$var</span></code>
`variable.property`             | Property of an object                           | <code class="keylighter inline"><span class="variable">$var</span>-&gt;<span class="variable property">property</span></code> <code class="keylighter inline">variable.<span class="variable property">property</span></code>
`call`                          | Functions/subroutine/directives calls           | <code class="keylighter inline"><span class="call">call</span>()</code>
`format`                        | Formatting styles                               | |
`format.italics`                | Italic text                                     | <code class="keylighter inline"><span class="format italics">italics</span></code>
`format.bold`                   | Bold text                                       | <code class="keylighter inline"><span class="format bold">bold</span></code>
`format.strike`                 | Strike text                                     | <code class="keylighter inline"><span class="format strike">strike</span></code>
`format.underline`              | Underlined text                                 | <code class="keylighter inline"><span class="format underline">underline</span></code>
`keyword`                       | Keywords                                        | <code class="keylighter inline"><span class="keyword">foreach</span></code>
`operator`                      | Operators                                       | <code class="keylighter inline"><span class="operator">+=</span></code> <code class="keylighter inline"><span class="operator">/</span></code> <code class="keylighter inline"><span class="operator">%</span></code>
`operator.punctuation`          | Punctuation operators                           | <code class="keylighter inline"><span class="operator punctuation">;</span></code> <code class="keylighter inline"><span class="operator punctuation">,</span></code> <code class="keylighter inline"><span class="operator punctuation">.</span> </code>
`operator.punctuation.brackets` | Various brackets                                | <code class="keylighter inline"><span class="operator punctuation brackets">()</span></code> <code class="keylighter inline"><span class="operator punctuation brackets">{}</span></code> <code class="keylighter inline"><span class="operator punctuation brackets">[]</span></code>
`delimiter`                     | Language/sections delimiters                    | <code class="keylighter inline"><span class="delimiter">&lt;?php</span></code> <code class="keylighter inline"><span class="delimiter">&lt;%</span></code>
`language.name`                 | Embedded `name` language                        | |
`comment`                       | Comment                                         | <code class="keylighter inline"><span class="comment">// some comment</span></code>
`comment.docblock`              | Documentation block comment                     | <code class="keylighter inline"><span class="comment docclock">/// Documentation comment</span></code>
`preprocessor`                  | Preprocessor definition                         | <code class="keylighter inline"><span class="preprocessor">#preprocessor directive</span></code>

Every language can actually define new top-level kinds of tokens if needed - but it is highly unrecommended as 
it'd be impossible for style makers to know them all. Instead of creating new top-level token kinds it's recommended to
stick to those predefined and extend them if needed. 

For example, in `LaTeX` we have math mode for defining math expressions, and we have no default token for such thing. 
So, instead of creating new top-level `math` token type, we should extend generic `expression` token and create 
`expression.math`. This way styles without knowledge about `LaTeX` specifics could still handle math as part of a 
generic expression.