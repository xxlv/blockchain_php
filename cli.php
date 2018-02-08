<?php
#!/usr/bin/env php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;


$application = new Application();

$application->setName('Blockchain in PHP');

$application->add(new \Bc\Command\MineCmd());
$application->add(new \Bc\Command\TransactionCmd());
$application->add(new \Bc\Command\WalletCmd());
$application->add(new \Bc\Command\WalletKeyCmd());
$application->add(new \Bc\Command\GenesisCmd());

$application->run();