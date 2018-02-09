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

class WalletAccounts extends Command
{
    protected function configure ()
    {
        $this
            ->setName('w:accounts')
            ->setDescription('List your account address');
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $wallet = new Wallet();
        $accounts = $wallet->accounts();
        $output->writeln([sprintf('Total Accounts: <info>%s</info>', count($accounts))]);
        $info = [];
        $index = 1;
        foreach ($accounts as $account) {
            $info[] = sprintf('Address #%s : <info>%s</info>  Public Key: <info>%s</info> ', $index++,
                $account['address'],
                $account['public_key']);
        }

        $output->writeln($info);
    }
}