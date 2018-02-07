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

class TransactionEvent extends Event
{
    public $event = 'TRANSACTION';

    public $data;

    public $dataMoon;


    public function __construct ($transaction)
    {
        $this->data = $transaction;
        $this->dataMoon = new SpaceX();
    }

    public function emit ()
    {
        $this->dataMoon->appendTransactionsToMemory($this->data);
    }


}