<?php

namespace DocCheck\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;

class DocCheck extends Command
{
    protected function configure()
    {
        $this->setName('DocCheck');
        $this->setDescription('Get the percentage of documentation coverage');
        $this->addOption('target', 't', InputOption::VALUE_REQUIRED,
            'The target where the documentation coverage is checked from');
        $this->addOption('error', 'e');
    }

    private function showError($targets, $input, $output) {
        $io = new SymfonyStyle($input, $output);

        $errorMessage = 'Target(s) not found:';
        foreach ($targets as $target) {
            $errorMessage .= PHP_EOL . "- $target";
        }
        $io->getErrorStyle()->error($errorMessage);
    }

    private function showProgress(OutputInterface $output)
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
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targets = explode(',', $input->getOption('target'));

        if ($input->getOption('error')) {
            $this->showError($targets, $input, $output);
            return;
        }
        $this->showProgress($output);
    }
}