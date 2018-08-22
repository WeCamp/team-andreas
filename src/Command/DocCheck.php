<?php

namespace DocCheck\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

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
        $numberOfFiles = 10;
        $progressBar = new ProgressBar($output, $numberOfFiles);
        $output->writeln("Now processing $numberOfFiles files:");
        $progressBar->start();
        for ($i = 0; $i < $numberOfFiles; $i++) {
            sleep(1);
            $progressBar->advance();
        }

        $progressBar->finish();
        $target = $input->getOption('target');
        $output->writeln($target);
        $output->writeln('Command is active');
    }
}