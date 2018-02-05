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

    /**
     * 仅仅矿工需要coin-base交易用来给自己以奖励
     *
     * @var
     */
    public $isCoinBase;


    public function createTransaction ($from, $to, $amount, $isCoinBase = false)
    {
        $this->from = $from;
        $this->to = $to;
        $this->amount = $amount;
        $this->isCoinBase = $isCoinBase;

        return $this;
    }


    public function isCoinBase ()
    {
        return $this->isCoinBase;
    }

    public function transaction ()
    {
        return (array)$this;
    }

    public function serialize ()
    {
        $transaction = $this->transaction();

        return json_encode((array)$transaction);
    }


    public function hash ()
    {
        return sha1(sha1($this->serialize()));
    }

}