<?php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\BlockChain\Tx;


class TxInput
{
    public $id;
    public $scriptSig;
    public $pubKey;

    public function __construct ($id, $scriptSig, $pubKey = null)
    {
        $this->id = $id;
        $this->scriptSig = $scriptSig;
        $this->pubKey = $pubKey;
    }
}