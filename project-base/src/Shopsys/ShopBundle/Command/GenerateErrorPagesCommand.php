<?php

namespace Shopsys\ShopBundle\Command;

use Shopsys\ShopBundle\Component\Error\ErrorPagesFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateErrorPagesCommand extends Command
{

    /**
     * @var \Shopsys\ShopBundle\Component\Error\ErrorPagesFacade
     */
    private $errorPagesFacade;

    /**
     * @param \Shopsys\ShopBundle\Component\Error\ErrorPagesFacade $errorPagesFacade
     */
    public function __construct(ErrorPagesFacade $errorPagesFacade)
    {
        $this->errorPagesFacade = $errorPagesFacade;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('shopsys:error-page:generate-all')
            ->setDescription('Generates all error pages for production.');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->errorPagesFacade->generateAllErrorPagesForProduction();
    }
}
