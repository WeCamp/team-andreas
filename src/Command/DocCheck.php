<?php

namespace DocCheck\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DocCheck extends Command
{
    protected function configure()
    {
        $this->setName('DocCheck');
        $this->setDescription('Get the percentage of documentation coverage');
        $this->addOption('target', 't', InputOption::VALUE_REQUIRED,
            'The target where the documentation coverage is checked from');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle ($input, $output);
        $target = $input->getOption('target');
        $style->title('Files missing documentation:');
        $style->listing(array(
            'src/index.php',
            'src/foo/bar.php',
            'next/index.php',
            'next/fizz/buzz.php',
        ));
        $style->newLine();
        $style->title('coverage:');
        $style->table(
            array('Target', 'Percentage'),
            array(
                array('src', '75%'),
                array('nxt', '80%'),
                array('total', '79%'),
            )
        );
    }
}