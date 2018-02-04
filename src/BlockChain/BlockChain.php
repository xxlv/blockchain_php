<?php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\BlockChain;


class BlockChain
{

    /**
     * 区块链,保存全部区块链数据
     *
     * @var array
     */
    protected $chain = [];

    /**
     * 当前待写入区块的交易记录
     *
     * @var array
     */
    protected $currentTransactions = [];


    /**
     * 当前区块的难度系数
     *
     * @var string
     */
    protected $difficulty = '0000';


    public function __construct ()
    {
        $this->initBlockChain();
    }

    public function hash ($block)
    {
        $block = json_encode($block);

        // TODO sha256
        return sha1($block);
    }

    public function mine ()
    {
        // 获取当前的交易信息
        $currentTransactions = $this->getCurrentTransactions();

        $chains = $this->chain;
        // 获取当前区块链上的最后一个区块

        if (count($chains) == 1) {
            $lastBlock = $chains[0];
        } else {
            $lastBlock = $chains[-1];
        }

        // 获取工作证明
        // 这里会是一个大量计算的循环
        $proof = $this->proofOfWork($lastBlock, $currentTransactions);

        // 创建block
        $this->newBlock($proof, $this->hash($lastBlock));
    }

    public function initBlockChain ()
    {
        // 创建创世区块
        // coinbase
        if (count($this->chain) < 1) {
            $this->newBlock(0, 0);
        }

    }

    public function newBlock ($proof, $preHash)
    {
        $block = [
            'index' => count($this->chain) + 1,
            'timestamp' => time(),
            'transactions' => $this->currentTransactions,
            'proof' => $proof,
            'previous_hash' => $preHash
        ];

        $this->appendToChain($block);

        return $block;
    }

    public function newTransaction ($from, $to, $amount)
    {
        $this->currentTransactions[] = (new Transaction())->createTransaction($from, $to, $amount)->transaction();
    }


    public function proofOfWork ($lastBlock, $currentTransactions)
    {
        $preHash = $this->hash($lastBlock);
        $guessProof = 0;
        // main Loop
        while (true) {
            $hash = sha1(sprintf('%s%s%s', $preHash, json_encode($currentTransactions), $guessProof));
            if (substr($hash, 0, strlen($this->getDifficulty())) === $this->getDifficulty()) {
                return $guessProof;
            }
            $guessProof++;
        }
    }

    protected function appendToChain ($block)
    {
        // TODO check hash

        $this->chain[] = $block;
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
    public function getChain ()
    {
        return $this->chain;
    }

    /**
     * @return array
     */
    public function getCurrentTransactions ()
    {
        return $this->currentTransactions;
    }


}



