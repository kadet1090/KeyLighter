<?php

return [
    [
        'Kadet\\Highlighter\\Language\\Apache',
        'name' => [
            'apache',
        ],
        'mime' => [
            'application/xml',
            'text/xml',
        ],
        'extension' => [
            '.htaccess',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Assembler',
        'name' => [
            'asm',
            'assembler',
        ],
        'mime' => [
            'text/x-asm',
        ],
        'extension' => [
            '*.asm',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Batch',
        'name' => [
            'bat',
            'batch',
            'dos',
        ],
        'mime' => [
            'application/bat',
            'application/x-bat',
            'application/x-msdos-program',
        ],
        'extension' => [
            '*.bat',
            '*.cmd',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\C',
        'name' => [
            'c',
        ],
        'mime' => [
            'text/x-csrc',
            'text/x-chdr',
        ],
        'extension' => [
            '*.c',
            '*.h',
            '*.idc',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\CSharp',
        'name' => [
            'CSharp',
            'C#',
        ],
        'mime' => [
            'text/x-csharp',
        ],
        'extension' => [
            '*.cs',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Cobol',
        'name' => [
            'cobol',
        ],
        'mime' => [
            'text/x-cobol',
        ],
        'extension' => [
            '*.cbl',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Cpp',
        'name' => [
            'cpp',
            'c++',
        ],
        'mime' => [
            'text/x-c++src',
            'text/x-c++hdr',
        ],
        'extension' => [
            '*.cpp',
            '*.hpp',
            '*.hxx',
            '*.cxx',
            '*.cc',
            '*.hh',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Css',
        'name' => [
            'css',
        ],
        'mime' => [
            'text/css',
        ],
        'extension' => [
            '*.css',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Css\\Less',
        'name' => [
            'less',
        ],
        'mime' => [
            'text/x-less',
        ],
        'extension' => [
            '*.less',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Css\\Sass',
        'name' => [
            'sass',
        ],
        'mime' => [
            'text/x-sass',
        ],
        'extension' => [
            '*.sass',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Css\\Scss',
        'name' => [
            'scss',
        ],
        'mime' => [
            'text/x-scss',
        ],
        'extension' => [
            '*.scss',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Dockerfile',
        'name' => [
            'dockerfile',
        ],
        'mime' => [],
        'extension' => [
            'Dockerfile',
            '*.Dockerfile',
            '*-Dockerfile',
            'Dockerfile.*',
            'Dockerfile-*',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Go',
        'name' => [
            'go',
            'golang',
        ],
        'mime' => [
            'text/x-go',
            'application/x-go',
            'text/x-golang',
            'application/x-golang',
        ],
        'extension' => [
            '*.go',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Haskell',
        'name' => [
            'haskell',
        ],
        'mime' => [
            'text/x-haskell',
        ],
        'extension' => [
            '*.hs',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Html',
        'name' => [
            'html',
        ],
        'mime' => [
            'text/html',
        ],
        'extension' => [
            '*.html',
            '*.htm',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Http',
        'name' => [
            'http',
        ],
        'mime' => [],
        'extension' => [],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Ini',
        'name' => [
            'ini',
        ],
        'mime' => [
            'text/x-ini',
            'text/inf',
        ],
        'extension' => [
            '*.ini',
            '*.cfg',
            '*.inf',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Java',
        'name' => [
            'java',
        ],
        'mime' => [
            'text/x-java',
        ],
        'extension' => [
            '*.java',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\JavaScript',
        'name' => [
            'js',
            'jscript',
            'javascript',
            'json',
        ],
        'mime' => [
            'application/javascript',
            'application/x-javascript',
            'text/x-javascript',
            'text/javascript',
            'application/json',
        ],
        'extension' => [
            '*.js',
            '*.jsx',
            '*.json',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Latex',
        'name' => [
            'tex',
            'latex',
        ],
        'mime' => [
            'application/x-tex',
            'application/x-latex',
        ],
        'extension' => [
            '*.tex',
            '*.aux',
            '*.toc',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Markdown',
        'name' => [
            'markdown',
            'md',
        ],
        'mime' => [
            'text/markdown',
        ],
        'extension' => [
            '*.markdown',
            '*.md',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Perl',
        'name' => [
            'perl',
        ],
        'mime' => [
            'text/x-perl',
            'application/x-perl',
        ],
        'extension' => [
            '*.pl',
            '*.pm',
            '*.t',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Php',
        'name' => [
            'php',
        ],
        'mime' => [
            'text/x-php',
            'application/x-php',
        ],
        'extension' => [
            '*.php',
            '*.phtml',
            '*.inc',
            '*.php?',
        ],
        'standalone' => true,
        'injectable' => true,
    ],
    [
        'Kadet\\Highlighter\\Language\\PlainText',
        'name' => [
            'plaintext',
            'text',
            'none',
        ],
        'mime' => [
            'text/plain',
        ],
        'extension' => [],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\PowerShell',
        'name' => [
            'powershell',
            'posh',
        ],
        'mime' => [
            'text/x-powershell',
            'application/x-powershell',
        ],
        'extension' => [
            '*.ps1',
            '*.psm1',
            '*.psd1',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Prolog',
        'name' => [
            'prolog',
        ],
        'mime' => [
            'text/x-prolog',
        ],
        'extension' => [
            '*.prolog',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Python',
        'name' => [
            'python',
            'py',
        ],
        'mime' => [
            'text/x-python',
            'application/x-python',
        ],
        'extension' => [
            '*.py',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Python\\Django',
        'name' => [
            'django',
            'jinja',
        ],
        'mime' => [
            'application/x-django-templating',
            'application/x-jinja',
        ],
        'extension' => [],
        'standalone' => false,
        'injectable' => true,
    ],
    [
        'Kadet\\Highlighter\\Language\\Ruby',
        'name' => [
            'ruby',
        ],
        'mime' => [
            'text/x-ruby',
            'application/x-ruby',
        ],
        'extension' => [
            '*.rb',
            '*.rbw',
            'Rakefile',
            '*.rake',
            '*.gemspec',
            '*.rbx',
            '*.duby',
            'Gemfile',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Shell',
        'name' => [
            'shell',
            'bash',
            'zsh',
            'sh',
        ],
        'mime' => [
            'text/x-shellscript',
            'application/x-shellscript',
        ],
        'extension' => [
            '*.sh',
            '*.zsh',
            '*.bash',
            '*.ebuild',
            '*.eclass',
            '*.exheres-0',
            '*.exlib',
            '.bashrc',
            'bashrc',
            '.bash_*',
            'bash_*',
            'PKGBUILD',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Sql',
        'name' => [
            'sql',
        ],
        'mime' => [
            'text/x-sql',
        ],
        'extension' => [
            '*.sql',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Sql\\MySql',
        'name' => [
            'mysql',
        ],
        'mime' => [
            'text/x-mysql',
        ],
        'extension' => [],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Twig',
        'name' => [
            'twig',
        ],
        'mime' => [
            'text/x-twig',
        ],
        'extension' => [
            '*.twig',
        ],
        'standalone' => false,
        'injectable' => true,
    ],
    [
        'Kadet\\Highlighter\\Language\\TypeScript',
        'name' => [
            'ts',
            'typescript',
        ],
        'mime' => [
            'application/typescript',
            'application/x-typescript',
            'text/x-typescript',
            'text/typescript',
        ],
        'extension' => [
            '*.ts',
            '*.tsx',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\UnifiedDiff',
        'name' => [
            'diff',
            'patch',
        ],
        'mime' => [
            'text/x-diff',
            'text/x-patch',
            'application/x-patch',
            'application/x-diff',
        ],
        'extension' => [
            '*.patch',
            '*.diff',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Xaml',
        'name' => [
            'xaml',
        ],
        'mime' => [],
        'extension' => [
            '*.xaml',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
    [
        'Kadet\\Highlighter\\Language\\Xml',
        'name' => [
            'xml',
        ],
        'mime' => [
            'application/xml',
            'text/xml',
        ],
        'extension' => [
            '*.xml',
        ],
        'standalone' => true,
        'injectable' => false,
    ],
];
