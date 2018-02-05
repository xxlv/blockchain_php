<?php


/*
 * This file is part of the blockchain_php.
 * (c) ghost <lvxiang119@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bc\Command;


use Bc\BlockChain\BlockChain;
use Bc\BlockChain\Wallet;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WalletCmd extends Command
{
    protected function configure ()
    {
        $this
            ->setName('w:balance')
            ->setDescription('Mini Wallet')
            ->addArgument('address')
            ->setHelp('Find your UXTO');
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {


        $wallet = new Wallet();
        $address = $input->getArgument('address');
        $balance = $wallet->balanceOf($address);

        if ($address) {
            $output->writeln([
                sprintf('Address: %s , Balance: %s', $address, $balance)
            ]);

        }


    }
}