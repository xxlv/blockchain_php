<?php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\BlockChain\Wallet;


use Bc\BlockChain\BlockChain;
use Bc\BlockChain\Event\Event;
use Bc\BlockChain\Network\Network;
use Bc\BlockChain\Event\TransactionEvent;
use Bc\BlockChain\Transaction;
use Bc\Tools\Hash;

class Wallet
{

    public function balanceOf ($walletAddress)
    {
        if ($this->verifyWalletAddress($walletAddress)) {
            $blockChain = new BlockChain();
            $UXTO = $blockChain->findUXTO($walletAddress);

            return $UXTO;
        }

        return 0;
    }

    public function newKeyPair ($phrase)
    {
        return KeyGenerator::newKeyPair($phrase);
    }

    public function verifyWalletAddress ($walletAddress)
    {
        if (is_string($walletAddress) && strlen($walletAddress) > 0) {
            return true;
        }

        return false;
    }

    public function verifyTransaction (Transaction $transaction)
    {
        //... check transaction

        $from = $transaction->getFrom();
        $to = $transaction->getTo();
        $amount = $transaction->getAmount();

        //TODO
        $currentSubsidy = 30;

        if ($transaction->isCoinBase()) {
            if (!$this->verifyWalletAddress($to)) {
                return false;
            }
            if ($amount <= $currentSubsidy) {
                return true;
            }
        } else {
            $verifyAddress = $this->verifyWalletAddress($from) && $this->verifyWalletAddress($to);
            if ($verifyAddress) {
                if ($this->verifyWalletAddressHasMoney($from, $amount)) {
                    //...
                    return true;
                }
            }

        }

        return false;
    }


    /**
     * Verify if the address has  money
     *
     * @param $address
     * @param $money
     *
     * @return bool
     */
    public function verifyWalletAddressHasMoney ($address, $money)
    {
        return true;
    }

    /**
     * make a script signature
     *
     * @param $address
     *
     * @return string
     */
    public function makeScriptSig ($address)
    {
        // TODO
        return Hash::hash('TODO');
    }


    public function newTransaction ($from, $to, $amount)
    {
        $transaction = (new Transaction())->createTransaction($from, $to, $amount);

        if ($this->verifyTransaction($transaction)) {
            //将交易发布到网络上
            $event = new TransactionEvent($transaction);

            Event::fire($event);
            // 将此消息广播到网络上
            Network::broadcast($event);
        }


        return $transaction;
    }
}