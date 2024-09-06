<?php

namespace Studip\Cli\Commands\Make;

use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;


final class Model extends Command
{
    protected static $defaultName = 'make:model';

    protected function configure(): void
    {
        $this->setDescription('Create a sorm-model file');
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the sorm-model');
        $this->addArgument('db-table', InputArgument::OPTIONAL, 'The name of the related db-table');
        $this->addOption('namespace', 's', InputOption::VALUE_OPTIONAL, 'Namespace', '');
        $defaultPath = $GLOBALS['STUDIP_BASE_PATH'] . '/lib/models';
        $this->addOption(
            'path',
            'p',
            InputOption::VALUE_OPTIONAL,
            'The location where the model file should be created',
            $defaultPath
        );
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $namespace = $input->getOption('namespace');
        $name      = $input->getArgument('name');
        $dbTable   = $input->getArgument('db-table');
        $path      = $input->getOption('path');
        $verbose   = $input->getOption('verbose');

        $filename = $this->createModelFile(
            $path,
            $name,
            $input,
            $output,
            $dbTable,
            $namespace
        );

        if ($verbose) {
            $output->writeln('Model file ' . $filename . ' created.');
        }

        return Command::SUCCESS;
    }

    private function createModelFile(
        string $path,
        string $name,
        InputInterface $input,
        OutputInterface $output,
        string $dbTable = null,
        string $namespace = null,
    ): string
    {
        if (!$dbTable) {
            $dbTable = strtosnakecase($name);
        }

        $file      = new PhpFile();
        $className = str_replace(' ', '', ucwords($name));

        if ($namespace) {
            $className = ucfirst($namespace) . '\\' . $className;
        }
        $class = $file->addClass($className);
        $class->setExtends(\SimpleORMap::class);
        $class->addComment(ucfirst($name) . '.php');
        $class->addComment('model class for table ' . $dbTable);
        $method = $class->addMethod('configure')
            ->setStatic()
            ->setProtected();

        $method->addBody(sprintf('$config[\'db_table\'] = \'%s\';', $dbTable));
        $method->addBody('parent::configure($config);');
        $method->addParameter('config', []);


        $printer = new PsrPrinter();
        $result  = $printer->printFile($file);

        $modelName = str_replace(' ', '_', ucfirst($name));
        $filename  = $path . '/' . $modelName . '.php';

        file_put_contents($filename, $result);

        $helper = $this->getHelper('question');

        $tableExists = \DBManager::get()->execute('SHOW TABLES LIKE ?', [$dbTable]);

        $describeModel = false;
        if ($tableExists) {
            $question = new ChoiceQuestion(
                "\nDescribe model:\n",
                $modelName
            );
            $describeModel  = $helper->ask($input, $output, $question);
        }

        if ($describeModel) {
            $greetInput = new ArrayInput([
                'command' => 'sorm:describe',
                'name'    => 'Fabien',
                '--yell'  => true,
            ]);

            $returnCode = $this->getApplication()->doRun($greetInput, $output);
        }

        return $filename;
    }
}
