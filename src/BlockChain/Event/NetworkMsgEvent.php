<?php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\BlockChain\Event;


use Bc\BlockChain\DataLayer\SpaceX;
use Bc\BlockChain\NetworkLayer\Network;

class NetworkMsgEvent extends Event
{
    public $event = 'NETWORK_MSG';

    public $data;

    public $dataMoon;


    public function __construct ($data)
    {
        $this->data = $data;
        $this->dataMoon = new SpaceX();
    }

    public function emit ()
    {
        //...
    }


}