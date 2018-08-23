<?php

namespace DocCheck\Command;

use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\ProjectFactory;
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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);

        if ($this->hasDocBlock()) {
            $style->writeln("We found it!");
        } else {
            $style->writeln("We did not found it");
            return;
        }

        $targets = explode(',', $input->getOption('target'));

        if ($input->getOption('error')) {
            $this->showError($targets, $style);
            return;
        }
        $this->showProgress($style, $output);
        $this->showOutput($style);
    }

    /**
     * @param string[] $targets
     * @param SymfonyStyle $style
     */
    private function showError(array $targets, SymfonyStyle $style)
    {
        $errorMessage = 'Target(s) not found:';
        foreach ($targets as $target) {
            $errorMessage .= PHP_EOL . "- $target";
        }
        $style->getErrorStyle()->error($errorMessage);
    }


    /**
     * @param SymfonyStyle $style
     * @param OutputInterface $output
     */
    private function showProgress(SymfonyStyle $style, OutputInterface $output)
    {
        $numberOfFiles = 10;
        $progressBar = new ProgressBar($output, $numberOfFiles);
        $style->writeln("Now processing $numberOfFiles files:");
        $progressBar->start();
        for ($i = 0; $i < $numberOfFiles; $i++) {
            sleep(1);
            $progressBar->advance();
        }

        $progressBar->finish();
    }


    /**
     * @param SymfonyStyle $style
     */
    private function showOutput(SymfonyStyle $style)
    {
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

    /**
     * @return bool
     */
    private function hasDocBlock(): bool
    {
        $projectFactory = ProjectFactory::createInstance();
        $files = [new LocalFile('tests/example.php')];
        $project = $projectFactory->create('MyProject', $files);
        $docblock = $project->getFiles()['tests/example.php']->getDocBlock();
        return $docblock !== null;
    }
}