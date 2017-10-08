<?php
/**
 * Highlighter
 *
 * Copyright (C) 2016, Some right reserved.
 *
 * @author Kacper "Kadet" Donat <kacper@kadet.net>
 *
 * Contact with author:
 * Xmpp: me@kadet.net
 * E-mail: contact@kadet.net
 *
 * From Kadet with love.
 */

namespace Kadet\Highlighter\Language;

use Kadet\Highlighter\Matcher\RegexMatcher;
use Kadet\Highlighter\Parser\Rule;
use Kadet\Highlighter\Parser\Token\LanguageToken;
use Kadet\Highlighter\Parser\TokenFactory;

class Http extends GreedyLanguage
{
    private $_embeddedFactory;

    public function __construct(array $options)
    {
        parent::__construct($options);

        $this->_embeddedFactory = new TokenFactory(LanguageToken::class);
    }

    /**
     * Tokenization rules setup
     */
    public function setupRules()
    {
        $this->rules->addMany([
            'number.status'   => new Rule(new RegexMatcher('/^HTTP\/.+\s(\d+)/')),
            'constant.status' => new Rule(new RegexMatcher('/^HTTP\/.+\s+\d+\s+(.+?)\R/')),

            'call.method' => new Rule(new RegexMatcher('/^(\w+).*HTTP\//')),
            'string.path' => new Rule(new RegexMatcher('/^\w+\s(.*?)\sHTTP\//')),

            'symbol.version' => new Rule(new RegexMatcher('/^.*(HTTP\/\S+)/')),

            'symbol.header' => new Rule(new RegexMatcher('/^([\w-]+:)/m')),
        ]);
    }

    public function tokenize($source, $additional = [], $offset = 0, $embedded = false)
    {
        $split = preg_split('/\R\R/', $source, 2);

        $http = $split[0];
        if(isset($split[1]) && $payload = $split[1]) {
            if(preg_match('/Content-Type: ([^;\r\n]*)/', $http, $matches)) {
                $mime = $matches[1];
            } else {
                $mime = 'text/plain';
            }

            $injected = self::byMime($mime);
            $language = $this->_embeddedFactory->create('language.'.$injected->getIdentifier(), [
                'pos'    => strlen($source) - strlen($payload) + $offset,
                'length' => strlen($payload),
                'inject' => $injected,
                'rule'   => new Rule(null, [
                    'language' => $this,
                    'priority' => 900
                ])
            ]);
            $language->setValid(true);

            $additional = array_merge(
                $additional,
                $injected->tokenize($payload, [], $language->pos, Language::EMBEDDED_BY_PARENT)->getArrayCopy(),
                [$language, $language->getEnd()]
            );
        }

        return parent::tokenize($source, $additional, $offset, $embedded);
    }


    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'http';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['http'],
        ];
    }
}
