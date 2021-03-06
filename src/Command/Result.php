<?php

namespace DocCheck\Command;

use DocCheck\Command\Result\Target;
use Symfony\Component\Console\Style\SymfonyStyle;
use League\Flysystem\Filesystem;

class Result
{
    /**
     * @var Target[]
     */
    private $targets = [];

    /**
     * @param FileSystem $fileSystem
     * @param SymfonyStyle $style
     */
    public function __construct($targets, FileSystem $fileSystem, SymfonyStyle $style) {
        foreach($targets as $target) {
            array_push($this->targets, new Target($target, $fileSystem, $style));
        }
    }

    public function getFailedFiles()
    {
        $failedFiles = [];
        foreach ($this->targets as $target) {
            $failedFiles += $target->getFailedFiles();
        }

        return $failedFiles;
    }

    public function getUnparsedFiles()
    {
        $unparsedFiles = [];
        foreach ($this->targets as $target) {
            $unparsedFiles += $target->getUnparsedFiles();
        }

        return $unparsedFiles;
    }


    public function getTotals()
    {
        $result = [];
        $totalFilteredFiles = 0;
        $totalFailedFiles = 0;
        foreach ($this->targets as $target) {
            $targetFailedFiles = count($target->getUnparsedFiles()) + count($target->getFailedFiles());
            $totalFiles = count($target->getFilteredFiles());
            if(count($this->targets) > 1) {
                $result[] = [
                    $target->getName(),
                    $totalFiles,
                    100 - number_format((float)($targetFailedFiles / $totalFiles * 100), 2, '.', '') . ' %'
                ];
            };
            $totalFilteredFiles += $totalFiles;
            $totalFailedFiles += $targetFailedFiles;

        }
        $result[] = [
            'total',
            $totalFilteredFiles,
            100 - number_format((float)($totalFailedFiles / $totalFilteredFiles * 100), 2, '.', '') . ' %'
        ];

        return $result;
    }


}