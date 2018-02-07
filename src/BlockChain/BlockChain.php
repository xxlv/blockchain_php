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
use Bc\Tools\Hash;
use Bc\Tools\Log;

class BlockChain
{

    public $id = 'SPACE-X-BLOCK-CHAIN-BY-GHOST';

    // Data layer
    public $dataMoon;
    /**
     * 区块链,保存全部区块链数据
     *
     * @var array
     */
    public $chains = [];

    public $nodes = [];

    public $currentBlockChainHeight = 0;

    public $currentBlock;

    /**
     * 当前待写入区块的交易记录
     *
     * @var array
     */
    public $currentTransactions = [];

    /**
     * 当前区块的难度系数
     *
     * @var string
     */
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
        $this->loadBlockChain();
        // init current blockchain params
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

            $this->newBlock(0, 0, []);
        }
    }

    protected function makeCoinBaseTransactions ()
    {
        $transaction = new Transaction();
        $from = '1993NWvTcDpVvpRTHjybyassDSH86UQgEA';
        $to = '163BGbKbW8PeC4GFgKS8XdgaaV3AUvtbdp';
        $subsidy = $this->getCurrentTransactions();

        $transaction->createTransaction($from, $to, $subsidy, true, $this);

        return $transaction;
    }

    public function initNodes ()
    {
        //$this->nodes = [
        //    new Node('127.0.0.1', '3321')
        //];
    }

    public function getCurrentSubsidy ()
    {
        return 50;
    }

    /**
     * 挖矿|打包区块
     */
    public function mine ()
    {
        // 获取当前的交易信息
        //$currentTransactions = $this->getCurrentTransactions();
        //$chains = $this->getChains();
        // 获取当前区块链上的最后一个区块
        //$lastBlock = $chains[$this->getCurrentBlockChainHeight()];

        $lastBlock = $this->getCurrentBlock();
        // 获取工作证明
        // 这里会是一个大量计算的循环
        $pow = $this->proofOfWork($lastBlock);


        $currentTransactions = $pow['currentTransactions'];
        $proof = $pow['proof'];


        // 创建block
        $this->newBlock($proof, $this->hash($lastBlock), $currentTransactions);
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
     * @param $currentTransactions
     *
     * @return array
     */
    public function newBlock ($proof, $preHash, $currentTransactions)
    {
        $blockChainHeight = $this->getCurrentBlockChainHeight();

        if ($blockChainHeight === 0) {
            $currentTransactions[] = $this->makeCoinBaseTransactions();
        }
        $block = new Block($blockChainHeight + 1, time(), $currentTransactions, $proof, $preHash);

        $this->appendToChain($block);

        return $block;
    }

    /**
     * 工作量证明
     *
     * @param $lastBlock
     *
     * @return int
     */
    public function proofOfWork ($lastBlock)
    {

        $preHash = $this->hash($lastBlock);
        $guessProof = 0;
        // main Loop
        while (true) {
            $currentTransactions = $this->getCurrentTransactions();
            //Only transactions found
            if ($currentTransactions) {
                $hash = Hash::hash(sprintf('%s%s%s', $preHash, Hash::makeHashAble($currentTransactions), $guessProof));
                if (substr($hash, 0, strlen($this->getDifficulty())) === $this->getDifficulty()) {
                    return ['proof' => $guessProof, 'currentTransactions' => $currentTransactions];
                }
                $guessProof++;
            }
        }
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
        return $this->chains;
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

    /**
     * @return int
     */
    public function getCurrentBlockChainHeight ()
    {
        return $this->dataMoon->getCurrentBlockChainHeight();
    }

}



