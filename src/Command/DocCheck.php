<?php
/**
 * This File is the part of Doc-Check
 *
 * @see the link to the documentation
 */
namespace DocCheck\Command;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\ProjectFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class
 *
 * @see expected link
 */
class DocCheck extends Command
{
    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var ProgressBar
     */
    private $progressBar;

    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        // Ensure that linked files are skipped as they are not supported
        // @todo show an error message when links are found and continue?
        $adapter = new Local(getcwd(), LOCK_EX, Local::SKIP_LINKS);
        $this->fileSystem = new Filesystem($adapter);
    }


    protected function configure()
    {
        $this->setName('DocCheck');
        $this->setDescription('Get the percentage of documentation coverage');
        $this->addOption('target', 't', InputOption::VALUE_REQUIRED,
            'The target where the documentation coverage is checked from');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targets = explode(',', $input->getOption('target'));

        $style = new SymfonyStyle($input, $output);
        $this->progressBar = new ProgressBar($output);

        $validationResult = $this->validateTargets($targets);

        if (count($validationResult)){
            $this->showError($validationResult, $style);
            return;
        }

        $totalFiles = 0;
        $targetFiles = [];
        foreach ($targets as $target) {
            $targetFiles[$target] = $this->fileSystem->listContents($target, true);
            $totalFiles =+ count($targetFiles[$target]);
        }

        $this->progressBar->setMaxSteps($totalFiles);

        $style->writeln("Now processing $totalFiles files:");
        $this->progressBar->start();

        $results = [];
        $totalFailed = 0;

        foreach ($targetFiles as $target => $files) {
            $phpFiles = array_filter($files, function ($entry) {
                return key_exists('extension', $entry) && $entry['extension'] == 'php';
            });

            $this->progressBar->advance(count($files) - count($phpFiles));

            $results[$target] = ['total' => count($phpFiles)];

            $total = count($phpFiles);
            $totalFiles = $totalFiles + $total;
            $results[$target] = ['total' => $total];
            $results[$target]['failedFiles'] = [];

            foreach ($phpFiles as $phpFile){
                if(!$this->hasDocumentationLink($phpFile['path'])){
                    $results[$target]['failedFiles'][] = $phpFile['path'];
                };
                $this->progressBar->advance();
            }

            $failedFiles = count($results[$target]['failedFiles']);
            $totalFailed =+ $failedFiles;
            $results[$target]['percentage'] = ($total - $failedFiles) / $total * 100;

        }
        $results['totalPercentage'] = ($totalFiles - $totalFailed) / $totalFiles *100;

        $this->progressBar->finish();
        $this->showOutput($style);
    }

    /**
     * @param string[] $targets
     * @param SymfonyStyle $style
     * @link some link to the documentation
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
     * @param string $filePath
     * @return bool
     */
    private function hasDocumentationLink(string $filePath): bool
    {
        $projectFactory = ProjectFactory::createInstance();
        $files = [new LocalFile($filePath)];
        $project = $projectFactory->create('MyProject', $files);
        $docblock = $project->getFiles()[$filePath]->getDocBlock();
        if($docblock === null) {
            return false;
        }
        return (count($docblock->getTagsByName('see')) > 0|| count($docblock->getTagsByName('link')) > 0);
    }

    /**
     * @param $targets string[]
     * @return string[]
     */
    private function validateTargets(array $targets): array
    {
        $validationResult = [];

        foreach ($targets as $target) {
            if (!$this->fileSystem->has($target)) {
                $validationResult[] = $target;
            }
        }

        return $validationResult;
    }
}