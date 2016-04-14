Token Types
===========

There are several standard token types available for use and styling.
In addition every type can have subtypes, for example we can have 
single quoted and double quoted string - both are strings, but different.
So we can define `string.single` and `string.double` both of them are
`string`s but `string.single` is not `string.double`. 

## Standard token types:
 - `string`    - String/Char literals
 - `number`    - Number literals integers, doubles, floats etc.
 - `symbol`    - Code symbols - class names, function names etc.
   - `symbol.class`      - Class Name
     - `symbol.class.interface` - Interface Name
   - `symbol.function`   - Function definition (**NOT CALL!**)
   - `symbol.annotation` - Annotation
   - etc.
 - `constant`  - Constants defined in language
 - `variable`  - Variables, including sigils (like $ in PHP)
   - `variable.property` - Property of object
 - `call`      - Functions/subroutine/directives calls
 - `format`    - Formatting styles
   - `format.italics`   - Italic text
   - `format.bold`      - Bold text
   - `format.strike`    - Strike text
   - `format.underline` - Underlined text
 - `keyword`   - Keywords
 - `operator`  - Operators
   - `operator.punctuation` - Punctuation operators i.e. `;` or `,`
 - `delimiter` - Language delimiters i.e. `<?php`
 - `language.name` - Embedded `name` language
 - `comment`   - Comment
   - `comment.docblock` - Documentation block comment 
 - `preprocessor` - Preprocessor definition

However every  language can define new types of tokens, but I 
suggest to stick with standard and just extend them. Create new tokens
only when it's really necessary and none from list above makes
semantic sense. 
