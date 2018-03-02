<?php

namespace Shopsys\FrameworkBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\DomainDataCreator;
use Shopsys\FrameworkBundle\Component\Domain\DomainDbFunctionsFacade;
use Shopsys\FrameworkBundle\Component\Domain\Multidomain\MultidomainEntityClassFinderFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDomainsDataCommand extends Command
{

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:domains-data:create';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\DomainDbFunctionsFacade
     */
    private $domainDbFunctionsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\DomainDataCreator
     */
    private $domainDataCreator;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Multidomain\MultidomainEntityClassFinderFacade
     */
    private $multidomainEntityClassFinderFacade;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Component\Domain\DomainDbFunctionsFacade $domainDbFunctionsFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\DomainDataCreator $domainDataCreator
     * @param \Shopsys\FrameworkBundle\Component\Domain\Multidomain\MultidomainEntityClassFinderFacade $multidomainEntityClassFinderFacade
     */
    public function __construct(
        EntityManagerInterface $em,
        DomainDbFunctionsFacade $domainDbFunctionsFacade,
        DomainDataCreator $domainDataCreator,
        MultidomainEntityClassFinderFacade $multidomainEntityClassFinderFacade
    ) {
        $this->em = $em;
        $this->domainDbFunctionsFacade = $domainDbFunctionsFacade;
        $this->domainDataCreator = $domainDataCreator;
        $this->multidomainEntityClassFinderFacade = $multidomainEntityClassFinderFacade;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Create new domains data');
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

    private function doExecute(OutputInterface $output)
    {
        $output->writeln('Start of creating new domains data.');

        $this->domainDbFunctionsFacade->createDomainDbFunctions();
        $domainsCreated = $this->domainDataCreator->createNewDomainsData();

        $output->writeln('<fg=green>New domains created: ' . $domainsCreated . '.</fg=green>');

        $multidomainEntitiesNames = $this->multidomainEntityClassFinderFacade->getMultidomainEntitiesNames();
        $output->writeln('<fg=green>Multidomain entities found:</fg=green>');
        foreach ($multidomainEntitiesNames as $multidomainEntityName) {
            $output->writeln($multidomainEntityName);
        }
    }
}
