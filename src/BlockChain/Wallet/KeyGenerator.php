<?php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\BlockChain\Wallet;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;

class KeyGenerator
{

    public static function newKeyPair ($phrase)
    {

        $adapter = EccFactory::getAdapter();
        $generator = EccFactory::getNistCurves()->generator384();
        $private = $generator->createPrivateKey();
        $derSerializer = new DerPrivateKeySerializer($adapter);
        $der = $derSerializer->serialize($private);
        echo sprintf("DER encoding:\n%s\n\n", base64_encode($der));
        $pemSerializer = new PemPrivateKeySerializer($derSerializer);
        $pem = $pemSerializer->serialize($private);

        echo sprintf("PEM encoding:\n%s\n\n", $pem);

        return [$private];
    }

    public static function makeRandomHex ()
    {
        return '';
    }

    public static function makePrivateKey ($hexString)
    {
        return $hexString;
    }

    public static function makePublicKey ($privateKey)
    {
        //...
    }

    public static function makePublicAddress ($publicKey)
    {
        //...
    }

    public static function base58Encode ()
    {
        //...
    }

    public static function generateRandomString ($length = 16)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

}