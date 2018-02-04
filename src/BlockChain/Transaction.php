<?php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\BlockChain;


class Transaction
{

    public $from;
    public $to;
    public $amount;

    public function createTransaction ($from, $to, $amount)
    {
        $this->from = $from;
        $this->to = $to;
        $this->amount = $amount;

        return $this;
    }

    public function transaction ()
    {
        return (array)$this;
    }


}