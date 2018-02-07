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

class TransactionCmd extends Command
{
    protected function configure ()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('bc:transaction')
            ->addArgument('from')
            ->addArgument('to')
            ->addArgument('mount')
            // the short description shown while running "php bin/console list"
            ->setDescription('发起一笔交易.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a new transaction...');
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {

        $wallet = new Wallet();
        $from = $input->getArgument('from');
        $to = $input->getArgument('to');
        $mount = $input->getArgument('mount');

        $output->writeln([
            sprintf('Transaction process success from %s to %s mount:%s!', $from, $to, $mount)
        ]);

        $transaction = $wallet->newTransaction($from, $to, $mount);

        $output->writeln([
            sprintf('Transaction hash is:%s!', $transaction->hash())
        ]);
    }
}