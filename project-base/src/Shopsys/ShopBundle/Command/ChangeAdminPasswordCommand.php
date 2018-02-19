<?php

namespace Shopsys\ShopBundle\Command;

use Shopsys\ShopBundle\Model\Administrator\AdministratorFacade;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class ChangeAdminPasswordCommand extends ContainerAwareCommand
{
    const ARG_USERNAME = 'username';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('shopsys:administrator:change-password')
            ->setDescription('Set new password for administrator.')
            ->addArgument(self::ARG_USERNAME, InputArgument::REQUIRED, 'Existing administrator username');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $administratorFacade = $this->getContainer()->get(AdministratorFacade::class);
        /* @var $administratorFacade \Shopsys\ShopBundle\Model\Administrator\AdministratorFacade */

        $adminUsername = $input->getArgument(self::ARG_USERNAME);
        $password = $this->askRepeatedlyForNewPassword($input, $io);

        $administratorFacade->changePassword($adminUsername, $password);

        $output->writeln(sprintf('Password for administrator "%s" was successfully changed', $adminUsername));
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Style\SymfonyStyle $io
     * @return string
     */
    private function askRepeatedlyForNewPassword(InputInterface $input, SymfonyStyle $io)
    {
        $question = new Question('Enter new password');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $question->setValidator(function ($password) use ($io) {
            if ($password == '') {
                throw new \Exception('The password cannot be empty');
            }

            $repeatQuestion = new Question('Repeat the password');
            $repeatQuestion->setHidden(true);
            $repeatQuestion->setHiddenFallback(false);
            $repeatQuestion->setValidator(function ($repeatedPassword) use ($password) {
                if ($repeatedPassword !== $password) {
                    throw new \Exception('Passwords do not match');
                }

                return $repeatedPassword;
            });
            $repeatQuestion->setMaxAttempts(1);

            return $io->askQuestion($repeatQuestion);
        });
        $question->setMaxAttempts(3);

        $password = $io->askQuestion($question);

        // Workaround for QuestionHelper that does not run validation in non-interactive mode
        // See: https://github.com/symfony/symfony/issues/23211
        if (!$input->isInteractive() && $password === null) {
            throw new \Exception('The password cannot be empty. Please run this command in interactive mode.');
        }

        return $password;
    }
}
