<?php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\BlockChain\Tx;


class TxOutput
{
    public $scriptPublicKey;
    public $value;

    public function __construct ($address, $value)
    {
        //...TODO
        $scriptPublicKey = $address;

        $this->scriptPublicKey = $scriptPublicKey;
        $this->value = $value;
    }

    public function verifySig ($scriptPublicKey)
    {
        $publicKey = $this->scriptPublicKey;
        //...
        return true;
    }

}