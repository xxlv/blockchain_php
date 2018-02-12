<?php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\BlockChain;


class Block
{

    public $index;
    public $timestamp;
    public $transactions;
    public $proof;
    public $previousHash;
    public $hash;

    public function __construct ($index, $timestamp, $transactions, $proof, $previousHash, $hash)
    {
        $this->index = $index;
        $this->timestamp = $timestamp;
        $this->transactions = $transactions;
        $this->previousHash = $previousHash;
        $this->proof = $proof;
        $this->hash = $hash;
    }

    public function block ()
    {
        return (array)$this;
    }

    public function computeUXTO ($address)
    {
        $transactions = $this->transactions;
        $currBlockUXTO = 0;
        foreach ($transactions as $transaction) {
            if (is_object($transaction)) {
                $transaction = $transaction->transaction();
            }

            if ($transaction['from'] == $address) {
                $currBlockUXTO -= $transaction['amount'];
            }
            if ($transaction['to'] == $address) {
                $currBlockUXTO += $transaction['amount'];
            }
        }

        return $currBlockUXTO;
    }

}