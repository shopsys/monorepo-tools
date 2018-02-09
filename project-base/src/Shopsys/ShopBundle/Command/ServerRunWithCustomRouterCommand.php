<?php

namespace Shopsys\ShopBundle\Command;

use Symfony\Bundle\WebServerBundle\Command\ServerRunCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Overrides default "server:run" command because web/index.php is used as front controller.
 *
 * Default behaviour of Symfony is to use router_<environment>.php that requires specific front controller.
 * Front controllers web/app.php and web/app_dev.php were removed because environment is determined by a file
 * in project root.
 */
class ServerRunWithCustomRouterCommand extends ServerRunCommand
{
    protected function configure()
    {
        parent::configure();

        $this->getDefinition()->getOption('router')->setDefault('app/router.php');
        $this->setName(self::$defaultName);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasParameterOption(['--env', '-e'])) {
            $io = new SymfonyStyle($input, $output);
            $io->error([
                'Environment passed in --env option is not supported.',
                'Environment can be set by file named DEVELOPMENT, PRODUCTION or TEST in project root.',
            ]);

            return 1;
        }

        return parent::execute($input, $output);
    }
}
