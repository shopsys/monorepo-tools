<?php

namespace SS6\ShopBundle\Command;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\DomainDataCreator;
use SS6\ShopBundle\Component\Domain\DomainDbFunctionsFacade;
use SS6\ShopBundle\Component\Domain\Multidomain\MultidomainEntityClassFinderFacade;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDomainsDataCommand extends ContainerAwareCommand {

	protected function configure() {
		$this
			->setName('ss6:domains-data:create')
			->setDescription('Create new domains data');
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$em = $this->getContainer()->get(EntityManager::class);
		/* @var $em \Doctrine\ORM\EntityManager */

		$em->transactional(function () use ($output) {
			$this->doExecute($output);
		});
	}

	private function doExecute(OutputInterface $output) {
		$output->writeln('Start of creating new domains data.');

		$domainDbFunctionsFacade = $this->getContainer()->get(DomainDbFunctionsFacade::class);
		/* @var $domainDbFunctionsFacade \SS6\ShopBundle\Component\Domain\DomainDbFunctionsFacade */
		$domainDbFunctionsFacade->createDomainDbFunctions();

		$domainDataCreator = $this->getContainer()->get(DomainDataCreator::class);
		/* @var $domainDataCreator \SS6\ShopBundle\Component\Domain\DomainDataCreator */
		$domainsCreated = $domainDataCreator->createNewDomainsData();

		$output->writeln('<fg=green>New domains created: ' . $domainsCreated . '.</fg=green>');

		$multidomainEntityClassFinderFacade = $this->getContainer()->get(MultidomainEntityClassFinderFacade::class);
		/* @var $multidomainEntityClassFinderFacade \SS6\ShopBundle\Component\Domain\Multidomain\MultidomainEntityClassFinderFacade */

		$multidomainEntitiesNames = $multidomainEntityClassFinderFacade->getMultidomainEntitiesNames();
		$output->writeln('<fg=green>Multidomain entities found:</fg=green>');
		foreach ($multidomainEntitiesNames as $multidomainEntityName) {
			$output->writeln($multidomainEntityName);
		}
	}

}
