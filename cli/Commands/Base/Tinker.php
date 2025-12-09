<?php

namespace Studip\Cli\Commands\Base;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psy\Configuration;
use Psy\Shell;
use Psy\VersionUpdater\Checker;
use SimpleCollection;
use SimpleORMap;

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
        $config->addCasters([
            SimpleCollection::class => TinkerCaster::class . '::castCollection',
            SimpleORMap::class => TinkerCaster::class . '::castModel',
        ]);

        $shell = new Shell($config);

        return $shell->run();
    }
}
