<?php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\BlockChain;

use Bc\BlockChain\DataLayer\SpaceX;
use Bc\BlockChain\Event\BlockEvent;
use Bc\BlockChain\Event\Event;
use Bc\Tools\Hash;
use Bc\Tools\Log;

class BlockChain
{
    public $id = 'SPACE-X-BLOCK-CHAIN-BY-GHOST';

    public $dataMoon;

    public $currentBlockChainHeight = 0;

    public $currentBlock;

    public $difficulty = '00';


    public function __construct ()
    {
        $this->dataMoon = new SpaceX();
        $this->initBlockChain();
    }


    public function findUXTO ($address)
    {
        $UXTO = 0;
        $blocks = $this->getChains();

        foreach ($blocks as $block) {
            if ($block) {
                $UXTO += $block->computeUXTO($address);
            }
        }

        return $UXTO;
    }


    public function initBlockChain ()
    {
        $this->loadBlockChain();
        $this->currentBlockChainHeight = $this->dataMoon->getCurrentBlockChainHeight();
        $this->currentBlock = $this->dataMoon->getCurrentBlock();
    }

    protected function loadBlockChain ()
    {
        $this->makeGenesisBlock();
    }

    protected function makeGenesisBlock ()
    {
        if ($this->getCurrentBlockChainHeight() < 1) {
            Log::info('Make genesis block');
            $this->newBlock(0, 0, [], time());
        }
    }

    protected function makeCoinBaseTransactions ()
    {
        $transaction = new Transaction();
        $from = '';
        $to = '1CXjoz2Tgqt5DJzp4NK19NUBonbzxgijGL';
        $subsidy = $this->getCurrentSubsidy();

        $transaction->createTransaction($from, $to, $subsidy, true, $this);

        return $transaction;
    }


    /**
     * coinbase的奖励
     *
     * @return int
     */
    public function getCurrentSubsidy ()
    {
        return 50;
    }

    /**
     * 挖矿|打包区块
     */
    public function mine ()
    {
        $lastBlock = $this->getCurrentBlock();
        $timestamp = time();

        $pow = $this->proofOfWork($lastBlock, $this->getCurrentLockedTransactions(), $timestamp);

        if ($this->verifyProofOfWork($pow)) {
            $currentTransactions = $pow['currentTransactions'];
            $proof = $pow['proof'];

            $newBlock = $this->newBlock($proof, $this->hash($lastBlock), $currentTransactions, $timestamp);
            if ($this->verifyBlock($newBlock)) {
                Event::fire(new  BlockEvent($newBlock));

            }
        }
    }

    /**
     * Verify proof of work
     *
     * @param $pow
     *
     * @return mixed
     */
    public function verifyProofOfWork ($pow)
    {
        return $pow;
    }

    /**
     * 验证一个block是否有效
     * 1. 检查一个区块是否合法
     * 2. 检查区块的proof是否合法
     * 3. 检查区块的交易是否都有效
     * 4. 检查区块的hash
     *
     * @param $block
     *
     * @return bool
     */
    public function verifyBlock (Block $block)
    {
        if ($this->isGenesisBlock($block)) {
            return true;
        }

        $blockData = $block->block();
        $hash = $this->blockHash($blockData['previousHash'], $blockData['transactions'], $blockData['proof'],
            $blockData['timestamp']);

        $lastBlock = $this->getBlockByIndex($block->index - 1);

        $previousHash = $this->hash($lastBlock);
        // check hash is equal
        if ($previousHash != $blockData['previousHash']) {
            return false;
        }
        if ($this->verifyBlockHash($hash)) {
            foreach ($blockData['transactions'] as $transaction) {
                if (!$this->verifyTransaction($transaction)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    public function isGenesisBlock (Block $block)
    {
        return $block->index == 1 && $block->previousHash == 0;
    }

    public function verifyBlockHash ($hash)
    {
        return $this->getDifficulty() == substr($hash, 0, strlen($this->getDifficulty()));
    }

    /**
     * 验证交易是否合法
     * 1. 检查交易的from
     * 2. 检查交易的to
     * 3. 检查交易的数额
     *
     * @param Transaction $transaction
     *
     * @return bool
     */
    public function verifyTransaction (Transaction $transaction)
    {
        $transactionArr = $transaction->transaction();
        if ($transaction->isCoinBase()) {
            return false;
        }
        $amount = $transactionArr['amount'];

        if ($amount <= 0) {
            return false;
        }

        return true;
    }


    /**
     * 创建一个block
     *
     * @param $proof
     * @param $preHash
     * @param $currentTransactions
     * @param $timestamp
     *
     * @return Block
     */
    public function newBlock ($proof, $preHash, $currentTransactions, $timestamp)
    {
        $blockChainHeight = $this->getCurrentBlockChainHeight();

        if ($blockChainHeight == 0) {
            $currentTransactions[] = $this->makeCoinBaseTransactions();
        }

        $block = new Block($blockChainHeight + 1, $timestamp, $currentTransactions, $proof, $preHash,
            $this->blockHash($preHash, $currentTransactions, $proof, $timestamp));

        if ($this->verifyBlock($block)) {
            $this->appendToChain($block);
        }

        return $block;
    }

    /**
     * Proof of work
     *
     * @param $lastBlock
     * @param $lockedTransactions
     * @param $lockedTime
     *
     * @return array
     */
    public function proofOfWork ($lastBlock, $lockedTransactions, $lockedTime)
    {
        $preHash = $this->hash($lastBlock);
        $guessProof = 0;
        //$lockedTransactions = $this->getCurrentLockedTransactions();
        if ($lockedTransactions) {
            //锁定池交易时间
            //$lockedTime = time();
            while (true) {
                $hash = $this->blockHash($preHash, $lockedTransactions, $guessProof, $lockedTime);

                if (substr($hash, 0, strlen($this->getDifficulty())) === $this->getDifficulty()) {
                    return ['proof' => $guessProof, 'currentTransactions' => $lockedTransactions];
                }
                $guessProof++;
            }
        }

        return [];
    }


    public function blockHash ($preHash, $transactions, $proof, $timestamp)
    {
        $hash = Hash::hash(sprintf('%s%s%s%s', $preHash, Hash::makeHashAble($transactions), $proof,
            $timestamp));

        return $hash;
    }

    protected function appendToChain ($block)
    {
        if ($this->verifyBlock($block)) {
            $this->dataMoon->appendToChain($block);
        }
    }

    /**
     * @return string
     */
    public function getDifficulty ()
    {
        return $this->difficulty;
    }

    /**
     * @return array
     */
    public function getChains ()
    {
        $index = 1;
        while (true) {
            if ($index > $this->getCurrentBlockChainHeight()) {
                break;
            }
            yield $this->dataMoon->getBlock($index);
            $index++;
        }

    }

    /**
     * 获取当前锁定的内存交易,这些交易将被打包进入下一个区块
     * 这些交易来源于当前的内存交易池
     *
     * @return array
     */
    public function getCurrentLockedTransactions ()
    {
        return $this->dataMoon->getCurrentTransactions();
    }

    /**
     * @return array
     */
    public function getCurrentTransactions ()
    {
        return $this->dataMoon->getCurrentTransactions();
    }


    /**
     * @return mixed
     */
    public function getCurrentBlock ()
    {
        return $this->dataMoon->getCurrentBlock();
    }

    public function getBlockByIndex ($index)
    {
        return $this->dataMoon->getBlockByIndex($index);
    }

    /**
     * @return int
     */
    public function getCurrentBlockChainHeight ()
    {
        return intval($this->dataMoon->getCurrentBlockChainHeight());
    }


    public function hash ($block)
    {
        return Hash::hash(Hash::makeHashAble($block));
    }

}



