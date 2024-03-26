<?php

namespace Studip\Cli\Commands\Base;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Psy\Configuration;
use Psy\Shell;
use Psy\VersionUpdater\Checker;

class Tinker extends Command
{
    protected static $defaultName = 'tinker';

    protected function configure(): void
    {
        $this->setDescription('Interact with your Stud.IP in a read-eval-print loop (REPL).');
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = Configuration::fromInput($input);
        $config->setUpdateCheck(Checker::NEVER);
        $config->setDefaultIncludes([__DIR__ . '/../../studip_cli_env.inc.php']);

        $shell = new Shell($config);

        return $shell->run();
    }
}
