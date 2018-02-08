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

class TransactionCmd extends Command
{
    protected function configure ()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('w:transaction')
            ->addArgument('from')
            ->addArgument('to')
            ->addArgument('amount')
            // the short description shown while running "php bin/console list"
            ->setDescription('Send a transaction.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a new transaction...');
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {

        $wallet = new Wallet();
        $from = $input->getArgument('from');
        $to = $input->getArgument('to');
        $amount = $input->getArgument('amount');

        $output->writeln([
            sprintf('Transaction process success from %s to %s amount:%s', $from, $to, $amount)
        ]);

        $transaction = $wallet->newTransaction($from, $to, $amount);
        if ($transaction) {
            $output->writeln([
                sprintf('Transaction hash is:%s', $transaction->hash())
            ]);
        } else {
            $output->writeln([
                sprintf('<error>Failed, from %s to %s, amount: %s </error>', $from, $to, $amount)
            ]);
        }

    }
}