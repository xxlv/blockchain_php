<?php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\BlockChain;


use Bc\BlockChain\Tx\TxInput;
use Bc\BlockChain\Tx\TxOut;

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

    public $txInput;
    public $txOutput;


    public function createTransaction ($from, $to, $amount, $isCoinBase = false)
    {
        $this->from = $from;
        $this->to = $to;
        $this->amount = $amount;
        $this->isCoinBase = $isCoinBase;

        $this->makeTxInput();
        $this->makeTxOutput();

        return $this;
    }


    public function isCoinBase ()
    {
        return $this->isCoinBase;
    }

    public function makeTxInput ()
    {
        $txInput = new TxInput($this->hash(), $this->makeScriptSig($this->from));

        $this->txInput = $txInput;
    }

    public function makeTxOutput ()
    {
        $txOutput = new TxOut($this->from, $this->amount);

        $this->txOutput = $txOutput;
    }

    public function makeScriptSig ($address)
    {
        $wallet = new Wallet();

        return $wallet->makeScriptSig($address);
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

    public function getTxInput ()
    {
        return $this->txInput;
    }

    public function getTxOutput ()
    {
        return $this->txOutput;
    }

    public function hash ()
    {
        return sha1(sha1($this->serialize()));
    }

}