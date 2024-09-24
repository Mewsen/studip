<?php

namespace Studip\Cli\Commands\DI;

use Studip\DIContainer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Reset extends Command
{
    protected static $defaultName = 'di:reset';

    protected function configure(): void
    {
        $this->setDescription('Resets the compiled DI container');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = DIContainer::getCompilationPath() . '/' . DIContainer::getCompilationClass() . '.php';

        if (file_exists($file) && !unlink($file)) {
            $output->writeln('<error>Could not removed compiled file.</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
