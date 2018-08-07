<?php

namespace Shopsys\FrameworkBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\DomainDataCreator;
use Shopsys\FrameworkBundle\Component\Domain\DomainDbFunctionsFacade;
use Shopsys\FrameworkBundle\Component\Domain\Multidomain\MultidomainEntityClassFinderFacade;
use Shopsys\FrameworkBundle\Model\Localization\DbIndexesFacade;
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
     * @var \Shopsys\FrameworkBundle\Model\Localization\DbIndexesFacade
     */
    private $dbIndexesFacade;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Component\Domain\DomainDbFunctionsFacade $domainDbFunctionsFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\DomainDataCreator $domainDataCreator
     * @param \Shopsys\FrameworkBundle\Component\Domain\Multidomain\MultidomainEntityClassFinderFacade $multidomainEntityClassFinderFacade
     * @param \Shopsys\FrameworkBundle\Model\Localization\DbIndexesFacade $dbIndexesFacade
     */
    public function __construct(
        EntityManagerInterface $em,
        DomainDbFunctionsFacade $domainDbFunctionsFacade,
        DomainDataCreator $domainDataCreator,
        MultidomainEntityClassFinderFacade $multidomainEntityClassFinderFacade,
        DbIndexesFacade $dbIndexesFacade
    ) {
        $this->em = $em;
        $this->domainDbFunctionsFacade = $domainDbFunctionsFacade;
        $this->domainDataCreator = $domainDataCreator;
        $this->multidomainEntityClassFinderFacade = $multidomainEntityClassFinderFacade;
        $this->dbIndexesFacade = $dbIndexesFacade;

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
        $this->dbIndexesFacade->updateLocaleSpecificIndexes();
        $output->writeln('<fg=green>All locale specific db indexes updated.</fg=green>');
    }
}
