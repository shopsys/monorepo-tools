<?php

namespace SS6\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProductVisibilityCommand extends ContainerAwareCommand {

	protected function configure() {
		$this
			->setName('ss6:product:visibility')
			->setDescription('Recalculate all product visibility');
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln('Start recalculating visibility of all products.');

		$productVisibilityFacade = $this->getContainer()->get('ss6.shop.product.product_visibility_facade');
		/* @var $productVisibilityFacade \SS6\ShopBundle\Model\Product\ProductVisibilityFacade */
		$productVisibilityFacade->refreshProductsVisibilityNow();

		$output->writeln('<fg=green>Visibility of all products was successfully recalculated.</fg=green>');
	}

}
