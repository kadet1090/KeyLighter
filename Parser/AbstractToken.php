<?php
/**
 * Created by PhpStorm.
 * User: k_don
 * Date: 16.08.2015
 * Time: 21:54
 */

namespace Kadet\Highlighter\Parser;


abstract class AbstractToken
{
    public $pos;
    public $name;

    public $rule;

    public $id;

    /**
     * AbstractToken constructor.
     */
    public function __construct($options)
    {
        if(isset($options[0])) {
            $this->name = $options[0];
        }

        foreach($options as $name => $value) {
            if(is_string($name)) {
                $this->{$name} = $value;
            }
        }
    }
}