<?php
/**
 * Created by PhpStorm.
 * User: k_don
 * Date: 16.08.2015
 * Time: 23:25
 */

namespace Kadet\Highlighter\Output;


interface OutputInterface
{
    public function format($source, array $tokens);
}