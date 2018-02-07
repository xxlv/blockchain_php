<?php


/*
 * This file is part of the Aidoctor.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\Tools;


class Serialize
{

    public static function serialize ($serializeAble)
    {
        return serialize($serializeAble);
    }

    public static function unSerialize ($serialized)
    {
        if (is_string($serialized)) {
            return unserialize($serialized);
        } else {
            return $serialized;
        }
    }
}