<?php

namespace SS6\ShopBundle\Command;

use SS6\ShopBundle\Model\Product\ProductVisibilityFacade;
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

		$productVisibilityFacade = $this->getContainer()->get(ProductVisibilityFacade::class);
		/* @var $productVisibilityFacade \SS6\ShopBundle\Model\Product\ProductVisibilityFacade */
		$productVisibilityFacade->refreshProductsVisibility();

		$output->writeln('<fg=green>Visibility of all products was successfully recalculated.</fg=green>');
	}

}
