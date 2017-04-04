<?php

namespace Shopsys\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ServerStartCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Overrides default "server:start" command because web/index.php is used as front controller.
 *
 * Default behaviour of Symfony is to use router_<environment>.php that requires specific front controller.
 * Front controllers web/app.php and web/app_dev.php were removed because environment is determined by a file
 * in project root.
 */
class ServerStartWithCustomRouterCommand extends ServerStartCommand
{
    protected function configure()
    {
        parent::configure();

        $this->getDefinition()->getOption('router')->setDefault('app/router.php');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasParameterOption(['--env', '-e'])) {
            $message = 'Environment passed in --env option is not supported. '
                . 'Environment can be set by file named DEVELOPMENT, PRODUCTION or TEST in project root.';
            $io = new SymfonyStyle($input, $output);
            $io->error($message);

            return 1;
        }

        return parent::execute($input, $output);
    }
}
