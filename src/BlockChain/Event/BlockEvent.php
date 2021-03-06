<?php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\BlockChain\Event;


use Bc\BlockChain\Block;
use Bc\BlockChain\DataLayer\SpaceX;
use Bc\BlockChain\NetworkLayer\Network;
use Bc\Tools\Log;

class BlockEvent extends Event
{
    public $event = 'BLOCK';

    public $data;

    public $dataMoon;


    public function __construct (Block $block)
    {
        $this->data = $block;
        $this->dataMoon = new SpaceX();
    }

    public function emit ()
    {
        Log::info(sprintf('new block created at %s', $this->data->index));
        Network::broadcast($this);
        $this->dataMoon->emptyTransactionsInMemory();

    }


}