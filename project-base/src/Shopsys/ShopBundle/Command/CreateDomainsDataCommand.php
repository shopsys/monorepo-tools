<?php

namespace Shopsys\ShopBundle\Command;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Domain\DomainDataCreator;
use Shopsys\ShopBundle\Component\Domain\DomainDbFunctionsFacade;
use Shopsys\ShopBundle\Component\Domain\Multidomain\MultidomainEntityClassFinderFacade;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDomainsDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('shopsys:domains-data:create')
            ->setDescription('Create new domains data');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /* @var $em \Doctrine\ORM\EntityManager */

        $em->transactional(function () use ($output) {
            $this->doExecute($output);
        });
    }

    private function doExecute(OutputInterface $output)
    {
        $output->writeln('Start of creating new domains data.');

        $domainDbFunctionsFacade = $this->getContainer()
            ->get('shopsys.shop.component.domain.domain_db_functions_facade');
        /* @var $domainDbFunctionsFacade \Shopsys\ShopBundle\Component\Domain\DomainDbFunctionsFacade */
        $domainDbFunctionsFacade->createDomainDbFunctions();

        $domainDataCreator = $this->getContainer()->get('shopsys.shop.component.domain.domain_data_creator');
        /* @var $domainDataCreator \Shopsys\ShopBundle\Component\Domain\DomainDataCreator */
        $domainsCreated = $domainDataCreator->createNewDomainsData();

        $output->writeln('<fg=green>New domains created: ' . $domainsCreated . '.</fg=green>');

        $multidomainEntityClassFinderFacade = $this->getContainer()
            ->get('shopsys.shop.component.domain.multidomain.multidomain_entity_class_finder_facade');
        /* @var $multidomainEntityClassFinderFacade \Shopsys\ShopBundle\Component\Domain\Multidomain\MultidomainEntityClassFinderFacade */

        $multidomainEntitiesNames = $multidomainEntityClassFinderFacade->getMultidomainEntitiesNames();
        $output->writeln('<fg=green>Multidomain entities found:</fg=green>');
        foreach ($multidomainEntitiesNames as $multidomainEntityName) {
            $output->writeln($multidomainEntityName);
        }
    }
}
