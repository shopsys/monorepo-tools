<?php

namespace SS6\ShopBundle\Command;

use SS6\ShopBundle\Component\Domain\DomainDataCreator;
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
		$output->writeln('Start of creating new domains data.');

		$domainDataCreator = $this->getContainer()->get(DomainDataCreator::class);
		/* @var $domainDataCreator \SS6\ShopBundle\Component\Domain\DomainDataCreator */
		$domainsCreated = $domainDataCreator->createNewDomainsData();

		$output->writeln('<fg=green>New domains created: ' . $domainsCreated . '.</fg=green>');
	}

}
