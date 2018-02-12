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

class BlockEvent extends Event
{
    public $event = 'BLOCK';

    public $data;

    public $dataMoon;


    public function __construct ($block)
    {
        $this->data = $block;
        $this->dataMoon = new SpaceX();
    }

    public function emit ()
    {
        Network::broadcast($this);
        $this->dataMoon->emptyTransactionsInMemory();

    }


}