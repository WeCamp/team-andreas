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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);
        $hasIt = $this->hasDocBlock();
        if ($hasIt) {
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

    private function showError($targets, $style) {

        $errorMessage = 'Target(s) not found:';
        foreach ($targets as $target) {
            $errorMessage .= PHP_EOL . "- $target";
        }
        $style->getErrorStyle()->error($errorMessage);
    }

    private function showProgress($style, $output)
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
     * @param $style
     */
    protected function showOutput($style)
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

    protected function hasDocBlock()
    {
        $projectFactory = ProjectFactory::createInstance();
        $files = [new LocalFile('tests/example.php')];
        $project = $projectFactory->create('MyProject', $files);
        $docblock = $project->getFiles()['tests/example.php']->getDocBlock();
        return $docblock !== null;
    }
}