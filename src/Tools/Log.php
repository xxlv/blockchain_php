<?php


/*
 * This file is part of the Aidoctor.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\Tools;


class Log
{

    public static function info ($str)
    {
        echo $str.PHP_EOL;

        return true;
    }

}