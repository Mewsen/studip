<?php

namespace Studip\Cli\Commands\Make;

use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

final class Plugin extends Command
{
    private const VALID_PLUGIN_INTERFACES = [
        \SystemPlugin::class,
        \StandardPlugin::class,
        \AdminCourseAction::class,
        \AdminCourseContents::class,
        \AdminCourseWidgetPlugin::class,
        \AdministrationPlugin::class,
        \DetailspagePlugin::class,
        \ExternPagePlugin::class,
        \FilesystemPlugin::class,
        \FileUploadHook::class,
        \ForumModule::class,
        \HomepagePlugin::class,
        \LibraryPlugin::class,
        \MetricsPlugin::class,
        \PortalPlugin::class,
        \PrivacyPlugin::class,
        \ScorePlugin::class,
        \QuestionnaireAssignmentPlugin::class,
    ];

    protected static $defaultName = 'make:plugin';

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'Name of the plugin');
        $this->addOption('origin', 'o', InputOption::VALUE_OPTIONAL, 'Origin of the plugin');
        $this->addOption('description', 'd', InputOption::VALUE_OPTIONAL, 'Description of the plugin');
        $this->addOption('plugin-version', 'pv', InputOption::VALUE_OPTIONAL, 'Version of the plugin');
        $this->addOption('min-version', 'min', InputOption::VALUE_OPTIONAL, 'Minimum version of Stud.IP the plugin supports');
        $this->addOption('max-version', 'max', InputOption::VALUE_OPTIONAL, 'Maximum version of Stud.IP the plugin supports');
        $this->addOption('plugin-interfaces', 'I', InputOption::VALUE_OPTIONAL, 'Comma separated list of plugin interfaces');
        $this->addOption('with-controller', 'c', InputOption::VALUE_OPTIONAL, 'Create default controller');
        $this->addOption('force', 'F', InputOption::VALUE_NEGATABLE, 'Force creation of the plugin (even if a plugin with that name and origin already exists)', false);
        $this->setDescription('Create a new plain plugin frame');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        // Get name of the plugin (if not already passed via command line)
        $name = $input->getArgument('name');
        if ($name === null) {
            $question = new Question('Please enter the name of the plugin: ');
            $question->setMaxAttempts(3);
            $question->setValidator(function ($name): string {
                if (!$name) {
                    throw new \RuntimeException('The name of the plugin is required');
                }

                return $name;
            });
            $question->setTrimmable(true);
            $name = $helper->ask($input, $output, $question);
        }

        // Get origin of the plugin (if not already passed via command line)
        $origin = $input->getOption('origin');
        if ($origin === null) {
            $question = new Question('Please enter the origin of the plugin: ');
            $question->setAutocompleterValues($this->getKnownOrigins());
            $question->setMaxAttempts(3);
            $question->setValidator(function ($origin): string {
                if (!$origin) {
                    throw new \RuntimeException('The origin of the plugin is required');
                }

                return $origin;
            });
            $question->setTrimmable(true);
            $origin = $helper->ask($input, $output, $question);
        }

        $interfaces = null;
        if ($input->hasOption('plugin-interfaces')) {
            $interfaces = explode(',', $input->getOption('plugin-interfaces'));
            $interfaces = array_filter($interfaces);
            $interfaces = array_intersect($interfaces, self::VALID_PLUGIN_INTERFACES);
            $interfaces = $interfaces ?: null;
        }
        $controllers = $input->getOption('with-controller');

        if (!$input->getOption('no-interaction')) {
            $version = $input->getOption('plugin-version');
            if ($version === null) {
                $question = new Question('Please enter the version of the plugin: ', '1.0');
                $version = $helper->ask($input, $output, $question);
            }

            $minVersion = $input->getOption('min-version');
            if ($minVersion === null) {
                $question = new Question('Please enter the studipMinVersion of the plugin: ', '');
                $minVersion = $helper->ask($input, $output, $question);
            }

            $maxVersion = $input->getOption('max-version');
            if ($maxVersion === null) {
                $question = new Question('Please enter the studipMaxVersion of the plugin: ', '');
                $maxVersion = $helper->ask($input, $output, $question);
            }

            $description = $input->getOption('description');
            if ($description === null) {
                $question = new Question('Please enter the description of the plugin: ');
                $description = $helper->ask($input, $output, $question);
            }

            if ($interfaces === null) {
                $question = new ChoiceQuestion(
                    'Please enter the interfaces of the plugin: ',
                    self::VALID_PLUGIN_INTERFACES,
                    0
                );
                $question->setMultiselect(true);
                $interfaces = $helper->ask($input, $output, $question);
            }

            if ($controllers === null) {
                $question = new ConfirmationQuestion(
                    'Should controller classes be created? (y/n) ',
                    false,
                    '/^(y|j)/i'
                );
                $controllers = $helper->ask($input, $output, $question);

                if ($controllers) {
                    $question = new ConfirmationQuestion(
                        'Do you want to define the controllers and actions interactively? (y/n) ',
                        false,
                        '/^(y|j)/i'
                    );
                    if ($helper->ask($input, $output, $question)) {
                        $controllers = [];

                        do {
                            $question = new Question('- Please enter the name of a controller: ');
                            $question->setMaxAttempts(3);
                            $question->setValidator(function ($controller): string {
                                if ($controller && preg_match('/[^a-z_]/', $controller)) {
                                    throw new \RuntimeException('The name of the controller may only contain letters and the underscore character.');
                                }

                                return strtolower($controller);
                            });
                            $question->setTrimmable(true);
                            $controller = $helper->ask($input, $output, $question);

                            if ($controller) {
                                $controllers[$controller] = [];

                                do {
                                    $question = new Question('- Please enter the name of an action (use special name !crud for the action "index", "edit", "store", "delete"): ');
                                    $question->setMaxAttempts(3);
                                    $question->setValidator(function ($action): string {
                                        if ($action === '!crud') {
                                            return $action;
                                        }

                                        if ($action && preg_match('/[^a-z_]/', $action)) {
                                            throw new \RuntimeException('The name of the action may only contain letters and the underscore character.');
                                        }

                                        return strtolower($action);
                                    });
                                    $question->setTrimmable(true);
                                    $action = $helper->ask($input, $output, $question);

                                    if ($action === '!crud') {
                                        $controllers[$controller][] = 'index';
                                        $controllers[$controller][] = 'edit';
                                        $controllers[$controller][] = 'store';
                                        $controllers[$controller][] = 'delete';
                                    } elseif ($action) {
                                        $controllers[$controller][] = $action;
                                    }
                                } while ($action !== '');
                            }

                        } while ($controller !== '');
                    }
                }
            }
        }

        // Cleanup
        $className = strtopascalcase($name);
        $interfaces = $interfaces ?? [\SystemPlugin::class];

        $pluginPath = $GLOBALS['STUDIP_BASE_PATH'] . "/public/plugins_packages/$origin/$className";

        if (
            file_exists($pluginPath)
            && !$input->getOption('force')
        ) {
            $question = new ConfirmationQuestion(
                'There is already a plugin with that origin and name. Overwrite? (y/n) ',
                false,
                '/^(y|j)/i'
            );
            if (!$helper->ask($input, $output, $question)) {
                $output->writeln('<error>Aborted');
                exit;
            }
        }

        mkdir($pluginPath, 0755, true);
        mkdir("$pluginPath/controllers", 0755, true);
        mkdir("$pluginPath/views", 0755, true);
        mkdir("$pluginPath/lib/classes/", 0755, true);
        mkdir("$pluginPath/lib/models", 0755, true);
        mkdir("$pluginPath/migrations", 0755, true);

        file_put_contents(
            "$pluginPath/plugin.manifest",
            $this->generatePluginManifest(
                $name,
                $className,
                $origin,
                $version ?? '',
                $minVersion ?? '',
                $maxVersion ?? '',
                $description ?? ''
            )
        );

        // Generate Plugin-Class
        $file = new PhpFile();
        $class = $file->addClass($className);
        $class->setExtends(\StudIPPlugin::class);
        foreach ($interfaces as $interface) {
            $class->addImplement($interface);
        }

        $method = $class->addMethod('__construct');
        $method->addBody('parent::__construct();');
        $method = $class->addMethod('perform');
        $method->addParameter('unconsumed_path');
        $method->addBody("//Import here styles or scripts for example");
        $method->addBody('parent::perform($unconsumed_path);');

        foreach ($interfaces as $interface) {
            foreach (get_class_methods($interface) as $method) {
                $class->inheritMethod($method);
            };
        }

        $printer  = new PsrPrinter();
        $result   = $printer->printFile($file);

        // Include requiring of bootstrap
        $result = str_replace(
            '<?php',
            '<?php' . PHP_EOL . 'require __DIR__ . \'/bootstrap.php\';' . PHP_EOL,
            $result
        );
        $filename = "$pluginPath/$className.php";
        file_put_contents($filename, $result);

        // Create bootstrap
        $bootstrap = implode(PHP_EOL, [
            '<?php',
            'StudipAutoloader::addAutoloadPath(__DIR__ . \'/lib/classes\');',
            'StudipAutoloader::addAutoloadPath(__DIR__ . \'/lib/models\');',
        ]);
        file_put_contents(
            "$pluginPath/bootstrap.php",
            $bootstrap
        );

        if ($controllers !== null) {
            $controllers = $this->createControllersAndView($controllers);

            // Create controllers and views
            foreach ($controllers as $controller_name => $actions) {
                $file = new PhpFile();
                $class = $file->addClass(strtopascalcase($controller_name . ' Controller'));
                $class->addProperty('_autobind', true);
                $class->setExtends(\PluginController::class);

                foreach ($actions as $action) {
                    $method = $class->addMethod("{$action}_action");
                    $method->addBody('//add your code here');
                }

                $printer = new PsrPrinter();
                $result = $printer->printFile($file);
                $filename = "{$pluginPath}/controllers/{$controller_name}.php";
                file_put_contents($filename, $result);

                $viewPath = "$pluginPath/views/$controller_name";
                mkdir($viewPath, 0755, true);

                foreach ($actions as $action) {
                    file_put_contents("{$viewPath}/{$action}.php", '');
                }
            }
        }

        $output->writeln('<info>Your plugin has been created!</info>');

        return Command::SUCCESS;
    }


    private function generatePluginManifest(
        string $name,
        string $class_name,
        string $origin,
        string $version,
        string $minVersion,
        string $maxVersion,
        string $description,
    ): string {
        if ($version === '') {
            $version = '1.0';
        }

        $manifest = "pluginname=$name\n";
        $manifest .= "pluginclassname=$class_name\n";
        $manifest .= "origin=$origin\n";
        $manifest .= "version=$version\n";

        if ($description) {
            $manifest .= "description=$description\n";
        }
        if ($minVersion) {
            $manifest .= "studipMinVersion=$minVersion\n";
        }
        if ($maxVersion) {
            $manifest .= "studipMaxVersion=$maxVersion\n";
        }

        return $manifest;
    }

    private function getKnownOrigins(): array
    {
        $origins = glob($GLOBALS['STUDIP_BASE_PATH'] . '/public/plugins_packages/*', GLOB_ONLYDIR);
        $origins = array_map('basename', $origins);
        natcasesort($origins);
        return $origins;
    }

    private function createControllersAndView(mixed $controllers): array
    {
        if ($controllers === true) {
            return ['show' => ['index']];
        }

        if (is_string($controllers)) {
            return [$controllers => ['index']];
        }

        return $controllers ?: [];
    }
}
