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


    public function verifyWalletAddress ($walletAddress)
    {
        if (is_string($walletAddress) && strlen($walletAddress) > 0) {
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
        // TODO
        return Hash::hash('TODO');
    }
}