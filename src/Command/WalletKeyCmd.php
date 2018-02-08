<?php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bc\Command;


use Bc\BlockChain\Wallet\Wallet;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WalletKeyCmd extends Command
{
    protected function configure ()
    {
        $this
            ->setName('w:keygen')
            ->setDescription('Create your account address')
            ->addArgument('phrase')
            ->setHelp('Create key pair');
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {


        $wallet = new Wallet();
        $phrase = $input->getArgument('phrase');
        $keyPair = $wallet->newKeyPair($phrase);
        $output->writeln([
            sprintf(json_encode($keyPair))
        ]);


    }
}