<?php

namespace SS6\ShopBundle\Command;

use SS6\ShopBundle\DataFixtures\Performance\CategoryDataFixture;
use SS6\ShopBundle\DataFixtures\Performance\OrderDataFixture;
use SS6\ShopBundle\DataFixtures\Performance\ProductDataFixture;
use SS6\ShopBundle\DataFixtures\Performance\UserDataFixture;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PerformanceDataCommand extends ContainerAwareCommand {

	protected function configure() {
		$this
			->setName('ss6:performance-data')
			->setDescription('Import performance data to test db. Demo and base data fixtures must be imported first.');
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$container = $this->getContainer();

		$categoryDataFixture = $container->get(CategoryDataFixture::class);
		/* @var $categoryDataFixture \SS6\ShopBundle\DataFixtures\Performance\ProductDataFixture */
		$productDataFixture = $container->get(ProductDataFixture::class);
		/* @var $productDataFixture \SS6\ShopBundle\DataFixtures\Performance\ProductDataFixture */
		$userDataFixture = $container->get(UserDataFixture::class);
		/* @var $userDataFixture \SS6\ShopBundle\DataFixtures\Performance\UserDataFixture */
		$orderDataFixture = $container->get(OrderDataFixture::class);
		/* @var $orderDataFixture \SS6\ShopBundle\DataFixtures\Performance\OrderDataFixture */

		$output->writeln('<fg=green>loading ' . CategoryDataFixture::class . '</fg=green>');
		$categoryDataFixture->load();
		$output->writeln('<fg=green>loading ' . ProductDataFixture::class . '</fg=green>');
		$productDataFixture->load($output);
		$output->writeln('<fg=green>loading ' . UserDataFixture::class . '</fg=green>');
		$userDataFixture->load();
		$output->writeln('<fg=green>loading ' . OrderDataFixture::class . '</fg=green>');
		$orderDataFixture->load();
	}

}
