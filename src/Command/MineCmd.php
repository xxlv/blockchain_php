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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MineCmd extends Command
{
    protected function configure ()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('bc:mine')
            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new block.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a new block...');
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {


        $blockChain = new BlockChain();
        $output->writeln([
            'prepare to mining...',
        ]);
        while (true) {

            $blockChain->mine();
            $output->writeln('Current Block' . json_encode($blockChain->getChain()));
            $output->writeln([
                'create block success!',
            ]);
        }

    }
}