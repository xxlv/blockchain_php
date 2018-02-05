<?php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\BlockChain;

use Bc\Tools\Hash;

class BlockChain
{

    /**
     * 区块链,保存全部区块链数据
     *
     * @var array
     */
    protected $chains = [];

    protected $nodes = [];

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
        $this->initNodes();

    }


    public function findUXTO ($address)
    {
        $UXTO = 0;
        $blocks = $this->getChains();

        foreach ($blocks as $block) {
            $UXTO += $block->computeUXTO($address);
        }

        return $UXTO;
    }

    public function hash ($block)
    {
        return Hash::hash(Hash::makeHashAble($block));
    }


    public function initBlockChain ()
    {
        $this->makeGenesisBlock();
    }


    protected function makeGenesisBlock ()
    {
        if (count($this->chains) < 1) {
            $this->newBlock(0, 0);
        }
    }

    protected function makeCoinBaseTransactions ()
    {
        $transaction = new Transaction();
        $from = '1993NWvTcDpVvpRTHjybyassDSH86UQgEA';
        $to = '163BGbKbW8PeC4GFgKS8XdgaaV3AUvtbdp';
        $mount = 50;
        $transaction->createTransaction($from, $to, $mount, true);

        return $transaction;
    }

    public function initNodes ()
    {
        $this->nodes = [
            new Node('127.0.0.1', '3321')
        ];
    }

    /**
     * 挖矿|打包区块
     */
    public function mine ()
    {
        // 获取当前的交易信息
        $currentTransactions = $this->getCurrentTransactions();
        $chains = $this->chains;
        // 获取当前区块链上的最后一个区块
        $lastBlock = $chains[count($chains) - 1];
        // 获取工作证明
        // 这里会是一个大量计算的循环
        $proof = $this->proofOfWork($lastBlock, $currentTransactions);

        // 创建block
        $this->newBlock($proof, $this->hash($lastBlock));
    }

    /**
     * 验证一个block是否有效
     *
     * @param $block
     *
     * @return bool
     */
    public function verifyBlock ($block)
    {
        // 检查一个区块是否合法
        // 检查区块的proof 是否合法
        // 检查区块的交易是否都有效
        // 检查区块的hash
        $block = $block->block();
        $needHash = [
            Hash::makeHashAble($block['timestamp']),
            Hash::makeHashAble($block['transactions']),
            Hash::makeHashAble($block['previousHash']),
            Hash::makeHashAble($block['proof']),
        ];
        $hash = Hash::hash(join('', $needHash));

        if ($this->getDifficulty() == substr($hash, 0, strlen($this->getDifficulty()))) {

            foreach ($block['transactions'] as $transaction) {
                if (!$this->verifyTransaction($transaction)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function verifyTransaction ($transaction)
    {
        //检查from
        //检查to
        //检查amount
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
     * 广播
     *
     * @return bool
     */
    public function broadcast ()
    {
        // get current block and broadcast it
        return true;
    }

    /**
     * 创建一个block
     *
     * @param $proof
     * @param $preHash
     *
     * @return array
     */
    public function newBlock ($proof, $preHash)
    {
        $blockChainHeight = count($this->chains);
        if ($blockChainHeight === 0) {
            $this->currentTransactions[] = $this->makeCoinBaseTransactions();
        }
        $block = new Block(count($this->chains) + 1, time(), $this->currentTransactions, $proof, $preHash);

        $this->appendToChain($block);

        //将currentTransactions 设置为空
        $this->currentTransactions = [];

        return $block;
    }

    public function newTransaction ($from, $to, $amount)
    {
        $this->currentTransactions[] = (new Transaction())->createTransaction($from, $to, $amount)->transaction();

        return $this;
    }

    /**
     * 工作量证明
     *
     * @param $lastBlock
     * @param $currentTransactions
     *
     * @return int
     */
    public function proofOfWork ($lastBlock, $currentTransactions)
    {
        $preHash = $this->hash($lastBlock);
        $guessProof = 0;
        // main Loop
        while (true) {
            //Only transactions found
            if ($currentTransactions) {
                $hash = Hash::hash(sprintf('%s%s%s', $preHash, Hash::makeHashAble($currentTransactions), $guessProof));
                if (substr($hash, 0, strlen($this->getDifficulty())) === $this->getDifficulty()) {
                    return $guessProof;
                }
                $guessProof++;
            } else {
                //...
            }

        }
    }


    protected function appendToChain ($block)
    {
        if ($this->verifyBlock($block)) {
            $this->chains[] = $block;
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
        return $this->chains;
    }

    /**
     * @return array
     */
    public function getCurrentTransactions ()
    {
        return $this->currentTransactions;
    }


}



