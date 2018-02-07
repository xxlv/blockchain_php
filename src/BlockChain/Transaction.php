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
use Bc\BlockChain\Tx\TxOutput;
use Bc\Tools\Hash;

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
    public $txInputMap;
    public $txOutputMap;


    public function createTransaction ($from, $to, $amount, $isCoinBase = false, $blockchain = null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->amount = $amount;
        $this->isCoinBase = $isCoinBase;

        $this->makeTxInput($blockchain);
        $this->makeTxOutput($blockchain);

        return $this;
    }

    public function isCoinBase ()
    {
        return $this->isCoinBase;
    }

    public function makeTxInput ($blockchain)
    {
        $scriptSig = $this->makeScriptSig($this->from);

        // first tx in
        if ($blockchain && count($blockchain->chains) < 1) {
            $scriptSig = 'The Times 03/Jan/2009 Chancellor on brink of second bailout for banks';
        }

        $txInput = new TxInput($this->hash(), $scriptSig);

        $this->txInputMap = [$txInput];
    }

    public function makeTxOutput ($blockchain)
    {
        $txOutput = new TxOutput($this->from, $this->amount);

        $this->txOutputMap = [$txOutput];
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
        return Hash::hash($this->serialize());
    }

}