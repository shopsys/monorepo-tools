<?php

namespace Shopsys\ShopBundle\Command;

use Shopsys\ShopBundle\Component\Error\ErrorPagesFacade;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateErrorPagesCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('shopsys:error-page:generate-all')
            ->setDescription('Generates all error pages for production.');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $errorPagesFacade = $this->getContainer()->get(ErrorPagesFacade::class);
        /* @var $errorPagesFacade \Shopsys\ShopBundle\Component\Error\ErrorPagesFacade */

        $errorPagesFacade->generateAllErrorPagesForProduction();
    }

}
