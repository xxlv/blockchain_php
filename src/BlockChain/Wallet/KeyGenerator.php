<?php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\BlockChain\Wallet;


class KeyGenerator
{

    public static function newKeyPair ($phrase)
    {
        $ecdsa = new Ecdsa();
        $ecdsa->generateRandomPrivateKey($phrase);
        $address = $ecdsa->getAddress();
        $privateKey = $ecdsa->getPrivateKey();

        $publicKey = $ecdsa->getPubKey();

        return ['address' => $address, 'private_key' => $privateKey, 'public_key' => $publicKey];
    }

}