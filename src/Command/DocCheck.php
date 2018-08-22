<?php

namespace DocCheck\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DocCheck extends Command
{
    protected function configure()
    {
        $this->setName('DocCheck');
        $this->setDescription('Bla');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Command is active');
    }
}