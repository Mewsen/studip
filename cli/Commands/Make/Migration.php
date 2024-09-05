<?php

namespace Studip\Cli\Commands\Make;

use Nette\PhpGenerator\PhpFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class Migration extends Command
{
    private const DEFAULT_BRANCH = '0';

    protected static $defaultName = 'make:migration';

    protected function configure(): void
    {
        $this->setDescription('Create a new migration file');
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the migration');
        $this->addOption(
            'branch',
            'b',
            InputOption::VALUE_OPTIONAL,
            'The branch of the migration file',
            self::DEFAULT_BRANCH
        );
        $this->addOption('domain', 'd', InputOption::VALUE_OPTIONAL, 'The domain of the migration file', 'studip');

        $defaultPath = $GLOBALS['STUDIP_BASE_PATH'] . '/db/migrations';
        $this->addOption(
            'path',
            'p',
            InputOption::VALUE_OPTIONAL,
            'The location where the migration file should be created',
            $defaultPath
        );
        $this->addOption(
            'description',
            'D',
            InputOption::VALUE_OPTIONAL,
            'The description for the migration',
            ''
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $branch      = $input->getOption('branch');
        $domain      = $input->getOption('domain');
        $name        = $input->getArgument('name');
        $path        = $input->getOption('path');
        $description = $input->getOption('description');
        $verbose     = $input->getOption('verbose');

        $version  = $this->getNextMigrationVersion($branch, $domain, $path, $verbose);
        $filename = $this->createMigrationFile($path, $version, $name, $description);

        if ($verbose) {
            $output->writeln('Migration file ' . $filename . ' created.');
        }

        return Command::SUCCESS;
    }

    private function getNextMigrationVersion(string $branch, string $domain, string $path, bool $verbose): string
    {
        $version     = new \DBSchemaVersion($domain, $branch);
        $migrator    = new \Migrator($path, $version, $verbose);
        $topVersions = $migrator->topVersion(true);

        if ($branch === self::DEFAULT_BRANCH) {
            $branches = array_keys($topVersions);
            usort($branches, 'version_compare');
            $branch = array_pop($branches);
        }

        return sprintf('%s.%s', $branch, isset($topVersions[$branch]) ? $topVersions[$branch] + 1 : 1);
    }

    private function createMigrationFile(string $path, string $version, string $name, string $description): string
    {
        if ($description === '') {
            $description = '// Add content';
        } else {
            $description = "return '$description';";
        }
        $file  = new PhpFile();
        $class = $file->addClass(str_replace(' ', '', ucwords($name)));
        $class
            ->setFinal()
            ->setExtends(\Migration::class)
            ->addComment("Description of class.\nSecond line\n");
        $class->addMethod('description')->addBody($description);
        $class->addMethod('up')->addBody('// Add content');
        $class->addMethod('down')->addBody('// Add content');

        $printer       = new StudipClassPrinter();
        $result        = $printer->printFile($file);
        $migrationName = $version . '_' . str_replace(' ', '_', lcfirst($name));
        $filename      = $path . '/' . $migrationName . '.php';

        file_put_contents($filename, $result);

        return $filename;
    }
}
