<?php

/**
 * Zelasli Framework
 * 
 * @author Rufai Limantawa <rufailimantawa@gmail.com>
 * @package Zelasli
 */

namespace Zelasli;


class Helpers
{
    /**
     * Inspect a variable
     *
     * Dumps information about a variable that includes its type and value 
     *
     * @var mixed $value
     *
     * @return void
     */
    public static function inspect($value): void
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
    }

    /**
     * Inspect a variable and die
     *
     * Dumps information about a variable that includes its type and value.
     * Similar to Helper::insect() function except that this function halt the
     * program execution
     *
     * @param mixed $value
     *
     * @return void
     */
    public static function inspectDie($value): void
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
    }
}