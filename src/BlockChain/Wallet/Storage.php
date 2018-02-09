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
use Bc\BlockChain\NetworkLayer\Network;
use Bc\BlockChain\Event\TransactionEvent;
use Bc\BlockChain\Transaction;
use Bc\Tools\Serialize;
use Predis\Client;

class Storage
{
    //const WALLET_PATH = '~/wallet_BLOCK_CHAIN_GHOST/';
    public $adapter;

    public function __construct ()
    {
        $sentinels = [];
        $options = [
            'parameters' => [
                'password' => 'helloworld',
                'database' => 11,
            ],
        ];

        $client = new Client($sentinels, $options);
        $this->adapter = $client;

    }

    public function set ($k, $v)
    {
        return $this->adapter->set($k, Serialize::serialize($v));
    }

    public function get ($k, $default = null)
    {
        return Serialize::unSerialize($this->adapter->get($k));
    }

    public function has ($k)
    {
        $v = $this->adapter->get($k);
        if ($v) {
            return true;
        } else {
            return false;
        }

    }

    public function destroy ()
    {
        return $this->adapter->flushall();
    }
}