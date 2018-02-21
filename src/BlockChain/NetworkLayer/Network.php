<?php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bc\BlockChain\NetworkLayer;


use Bc\BlockChain\Event\Event;
use Bc\BlockChain\Event\NetworkMsgEvent;
use Bc\Tools\Log;

class Network
{
    const ADDRESS = '127.0.0.1';
    const PORT = 8014;
    const PACKAGE_SIZE = 10240;

    public static function broadcast ($event)
    {
        // TODO
    }


    public static function ping ()
    {
        $port = self::PORT;
        $address = self::ADDRESS;

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
        } else {
            echo "OK. \n";
        }

        echo "Attempting to connect to '$address' on port '$port'...";
        $result = socket_connect($socket, $address, $port);
        if ($result === false) {
            echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
        } else {
            echo "OK \n";
        }
        $in = 'pang';
        echo "sending http head request ...";
        socket_write($socket, $in, strlen($in));
        echo "OK\n";

        echo "Reading response:\n\n";
        while ($out = socket_read($socket, 8192)) {
            echo $out;
        }
        echo "closeing socket..";
        socket_close($socket);
        echo "ok .\n\n";
    }

    public static function run ()
    {
        $address = self::ADDRESS;
        $port = self::PORT;
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket) {
            socket_bind($socket, $address, $port);
            socket_listen($socket, 5);
            Log::info('Listen on ' . $address . ':' . $port);
            do {
                $socketResource = socket_accept($socket);
                $info = socket_read($socketResource, self::PACKAGE_SIZE);
                Network::protocolMsgProcess($info);

            } while (true);

        }
    }

    public static function protocolMsgProcess ($msg)
    {
        Log::info('[TODO] New msg >>' . $msg);
        Event::fire(new NetworkMsgEvent($msg));
    }
}