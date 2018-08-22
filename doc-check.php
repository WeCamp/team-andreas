#!/usr/bin/env php
<?php

// Ensure we have the autloader loaded and working

foreach (array(__DIR__ . '/../../autoload.php', __DIR__ . '/../vendor/autoload.php', __DIR__ . '/vendor/autoload.php') as $file) {
    if (file_exists($file)) {
        require $file;
        break;
    }
}

use Symfony\Component\Console\Application;
use \DocCheck\Command\DocCheck;

$command = new DocCheck();

$application = new Application();
$application->add($command);
$application->setDefaultCommand($command->getName());
$application->run();