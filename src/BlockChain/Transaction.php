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
use Bc\BlockChain\Wallet\Wallet;
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

        if ($blockchain && $blockchain->getCurrentBlockChainHeight() < 1) {
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


    /**
     * @return mixed
     */
    public function getFrom ()
    {
        return $this->from;
    }

    /**
     * @return mixed
     */
    public function getTo ()
    {
        return $this->to;
    }

    /**
     * @return mixed
     */
    public function getAmount ()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getIsCoinBase ()
    {
        return $this->isCoinBase;
    }

    /**
     * @return mixed
     */
    public function getTxInputMap ()
    {
        return $this->txInputMap;
    }

    /**
     * @return mixed
     */
    public function getTxOutputMap ()
    {
        return $this->txOutputMap;
    }

    public function hash ()
    {
        return Hash::hash($this->serialize());
    }

}