<?php

namespace Studip\Cli\Commands\User;

use email_validation_class;
use StudipLog;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\Question;
use UserManagement;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ChangePassword extends Command
{
    protected static $defaultName = 'user:password';

    protected function configure(): void
    {
        $this->setDescription('Change the password of a Stud.IP user.');
        $this->setHelp('This command will change the password of a user.');
        $this->addArgument(
            'username',
            InputArgument::REQUIRED,
            'The username of the user whose password will be changed.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');

        $user = \User::findOneBySQL('username = ?', [$username]);
        if (!$user) {
            $output->writeln('<error>Could not find this user.</error>');
            return Command::FAILURE;
        }

        $helper = $this->getHelper('question');

        $question = new Question('New password: ', '');
        $question->setHidden(true);
        $password = $helper->ask($input, $output, $question);

        $question2 = new Question('Re-type password: ', '');
        $question2->setHidden(true);
        $password2 = $helper->ask($input, $output, $question2);

        $status = $this->changePassword($user, $password, $password2);
        if (isset($status)) {
            $output->writeln('<error>' . $status . '</error>');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function changePassword(\User $user, string $password, string $password2): ?string
    {
        if ($password !== $password2) {
            return 'Password and re-type password don\'t match.';
        }

        $validator = new email_validation_class();
        $validator->timeout = 10;
        if (!$validator->ValidatePassword($password)) {
            return 'The password is too short. It should have at least 8 characters.';
        }

        if ($password === $user->username) {
            return 'Password may not match the username.';
        }

        if (str_replace(['.', ' '], '', mb_strtolower($password)) == 'studip') {
            return 'For security reasons the password may not be "Stud.IP" or any modification of "Stud.IP".';
        }

        $userManagement = new UserManagement($user->id);
        $changed = $userManagement->changePassword($password);

        if (!$changed) {
            return 'The password could not be set.';
        }

        StudipLog::USER_NEWPWD($user->id, null, 'Passwort neu gesetzt', null, $user->id);

        return null;
    }
}
