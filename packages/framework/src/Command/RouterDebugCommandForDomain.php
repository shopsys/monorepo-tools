<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\Component\Console\DomainChoiceHandler;
use Symfony\Bundle\FrameworkBundle\Command\RouterDebugCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RouterDebugCommandForDomain extends RouterDebugCommand
{
    /**
     * @var string
     */
    protected static $defaultName = 'debug:router';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Console\DomainChoiceHandler
     */
    private $domainChoiceHelper;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Console\DomainChoiceHandler $domainChoiceHelper
     * @param \Symfony\Component\Routing\RouterInterface|null $router
     */
    public function __construct(DomainChoiceHandler $domainChoiceHelper, $router = null)
    {
        $this->domainChoiceHelper = $domainChoiceHelper;

        parent::__construct($router);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->domainChoiceHelper->chooseDomainAndSwitch($io);

        return parent::execute($input, $output);
    }
}
