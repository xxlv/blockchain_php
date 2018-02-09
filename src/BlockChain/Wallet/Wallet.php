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
use Bc\BlockChain\Event\TransactionEvent;
use Bc\BlockChain\Transaction;

class Wallet
{

    public $ecdsa;
    public $blockchain;
    public $storage;

    public function __construct ()
    {
        $this->ecdsa = new Ecdsa();
        $this->storage = new Storage();
    }

    /**
     * Get balance of specify address
     *
     * @param $walletAddress
     *
     * @return int
     */
    public function balanceOf ($walletAddress)
    {
        if ($this->verifyWalletAddress($walletAddress)) {
            $blockChain = new BlockChain();
            $UXTO = $blockChain->findUXTO($walletAddress);

            return $UXTO;
        }

        return 0;
    }


    public function accounts ()
    {
        $prefix = 'WALLET_ADDRESS_';
        $keys = $this->storage->adapter->keys($prefix . '*');

        $accounts = [];
        foreach ($keys as $key) {
            if ($this->storage->has($key)) {
                $accounts[$key] = $this->storage->get($key);
            }
        }

        return $accounts;
    }

    public function newKeyPair ($phrase)
    {
        $prefix = 'WALLET_ADDRESS_';
        $keyPair = KeyGenerator::newKeyPair($phrase);
        $this->storage->set($prefix . $keyPair['address'], $keyPair);

        return $keyPair;
    }

    public function verifyWalletAddress ($walletAddress)
    {
        return $this->ecdsa->validateAddress($walletAddress);
    }

    /**
     * 验证一笔交易是否合法
     *
     *
     * @param Transaction $transaction
     *
     * @return bool
     */
    public function verifyTransaction (Transaction $transaction)
    {

        $from = $transaction->getFrom();
        $to = $transaction->getTo();
        $amount = $transaction->getAmount();
        $currentSubsidy = (new BlockChain())->getCurrentSubsidy();

        if ($transaction->isCoinBase()) {
            if (!$this->verifyWalletAddress($to)) {
                return false;
            }
            if ($amount <= $currentSubsidy) {
                return true;
            }
        } else {
            //自己给自己发起交易,这里认为是非法的
            $verifyAddress = $this->verifyWalletAddress($from) && $this->verifyWalletAddress($to) && $from != $to;
            if ($verifyAddress) {
                if ($this->verifyWalletAddressHasMoney($from, $amount)) {
                    return true;
                }
            }

        }

        return false;
    }


    /**
     * 验证指定的地址是否有足够的余额
     *
     * @param $address
     * @param $money
     *
     * @return bool
     */
    public function verifyWalletAddressHasMoney ($address, $money)
    {
        $uxto = (new BlockChain())->findUXTO($address);

        if ($uxto && $uxto >= $money) {
            return true;
        }

        return false;
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
        $msg = 'BLOCKCHAIN_' . date('Y-m-dHis') . random_int(1, 10000);

        $privateKey = $this->getPrivateKey($address);
        if ($privateKey) {
            $this->ecdsa->setPrivateKey($privateKey);
            $sig = $this->ecdsa->signMessage($msg, true);
        } else {
            $sig = false;
        }

        return ['origin' => $msg, 'scriptSig' => $sig];
    }

    public function getPrivateKey ($address)
    {
        //TODO load private key from wallet
        return '';
    }

    /**
     * 发起一笔交易
     *
     * -_-
     * 验证交易时候是否合法,包括
     * 1. 验证交易的双方地址
     * 2. 交易者是否提供了合法的sig以证明自己对该账号的UXTO有控制权
     * 3. 检查交易的发起者是否拥有足够的UXTO
     * 4. 检查当前的交易账号是否被锁定
     * 当交易通过后,通过触发交易事件,事件将被广播到网络,其他节点可以接受该事件消息并更新自身的区块链
     * -_-
     *
     * @param $from
     * @param $to
     * @param $amount
     *
     * @return $this|bool
     */
    public function newTransaction ($from, $to, $amount)
    {
        $transaction = (new Transaction())->createTransaction($from, $to, $amount);

        if ($this->verifyTransaction($transaction)) {
            $event = new TransactionEvent($transaction);
            Event::fire($event);

            return $transaction;

        } else {
            return false;
        }
    }
}