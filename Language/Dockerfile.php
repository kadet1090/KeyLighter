<?php

declare(strict_types=1);

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

class Dockerfile extends Shell
{
    public function setupRules()
    {
        parent::setupRules();

        $keywords = [
            'ADD', 'ARG', 'AS', 'CMD', 'COPY', 'ENTRYPOINT', 'ENV', 'EXPOSE', 'FROM',
            'HEALTHCHECK', 'LABEL', 'MAINTAINER', 'ONBUILD', 'RUN', 'SHELL', 'STOPSIGNAL',
            'USER', 'VOLUME', 'WORKDIR',
        ];

        $this->rules->add('keyword', new Rule(
            new RegexMatcher('/^\s*(' . implode('|', $keywords) . ')\b/mi'),
            ['priority' => 2]
        ));
    }

    /**
     * Unique language identifier, for example 'php'
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'dockerfile';
    }

    public static function getMetadata()
    {
        return [
            'name'      => ['dockerfile'],
            'extension' => ['Dockerfile', '*.Dockerfile', '*-Dockerfile', 'Dockerfile.*', 'Dockerfile-*'],
        ];
    }
}
