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

$application->add(new \Bc\Command\Mine());
$application->add(new \Bc\Command\Transaction());
$application->add(new \Bc\Command\Wallet());
$application->add(new \Bc\Command\WalletKey());
$application->add(new \Bc\Command\WalletAccounts());
$application->add(new \Bc\Command\Genesis());

$application->run();