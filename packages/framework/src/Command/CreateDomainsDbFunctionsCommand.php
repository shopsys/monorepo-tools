<?php

namespace Shopsys\FrameworkBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\DomainDbFunctionsFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDomainsDbFunctionsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:domains-db-functions:create';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\DomainDbFunctionsFacade
     */
    private $domainDbFunctionsFacade;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Component\Domain\DomainDbFunctionsFacade $domainDbFunctionsFacade
     */
    public function __construct(EntityManagerInterface $em, DomainDbFunctionsFacade $domainDbFunctionsFacade)
    {
        $this->em = $em;
        $this->domainDbFunctionsFacade = $domainDbFunctionsFacade;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Create new domains DB functions');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em->transactional(function () use ($output) {
            $this->doExecute($output);
        });
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function doExecute(OutputInterface $output)
    {
        $output->writeln('Start of creating db functions.');

        $this->domainDbFunctionsFacade->createDomainDbFunctions();

        $output->writeln('<fg=green>All db functions created.</fg=green>');
    }
}
