<?php


/*
 * This file is part of the Aidoctor.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\Tools;


class Hash
{

    public static function hash ($str)
    {
        return sha1($str);
    }

    public static function makeHashAble ($data)
    {
        if (is_string($data)) {
            return $data;
        }

        $data = (array)$data;

        return json_encode($data);
    }
}