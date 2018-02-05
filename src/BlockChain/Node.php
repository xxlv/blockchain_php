<?php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\BlockChain;


use Bc\Tools\Hash;

class Node
{
    protected $address;
    protected $port;
    protected $id;

    public function __construct ($address, $port)
    {
        $this->address = $address;
        $this->port = $port;
        $this->id = $this->makeId($address, $port, $version = 1);
    }

    protected function makeId ($address, $port, $version)
    {
        return $version . Hash::hash($address . $port);
    }

}