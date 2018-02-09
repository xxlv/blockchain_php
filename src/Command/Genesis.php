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

class Genesis extends Command
{
    protected function configure ()
    {
        $this
            ->setName('genesis:init')
            ->setDescription('Init a blockchain')
            ->setHelp('Init');
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $bc = new BlockChain();
        $bc->dataMoon->empty();
    }
}