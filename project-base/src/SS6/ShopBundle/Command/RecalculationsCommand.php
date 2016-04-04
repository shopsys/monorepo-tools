<?php

namespace SS6\ShopBundle\Command;

use SS6\ShopBundle\Model\Category\CategoryVisibilityRepository;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;
use SS6\ShopBundle\Model\Product\ProductHiddenRecalculator;
use SS6\ShopBundle\Model\Product\ProductSellingDeniedRecalculator;
use SS6\ShopBundle\Model\Product\ProductVisibilityFacade;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecalculationsCommand extends ContainerAwareCommand {

	protected function configure() {
		$this
			->setName('ss6:recalculations')
			->setDescription('Run all recalculations.');
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$categoryVisibilityRepository = $this->getContainer()->get(CategoryVisibilityRepository::class);
		/* @var $categoryVisibilityRepository \SS6\ShopBundle\Model\Category\CategoryVisibilityRepository */
		$productHiddenRecalculator = $this->getContainer()->get(ProductHiddenRecalculator::class);
		/* @var $productHiddenRecalculator \SS6\ShopBundle\Model\Product\ProductHiddenRecalculator */
		$productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */
		$productVisibilityFacade = $this->getContainer()->get(ProductVisibilityFacade::class);
		/* @var $productVisibilityFacade \SS6\ShopBundle\Model\Product\ProductVisibilityFacade */
		$productAvailabilityRecalculator = $this->getContainer()->get(ProductAvailabilityRecalculator::class);
		/* @var $productAvailabilityRecalculator \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator */
		$productSellingDeniedRecalculator = $this->getContainer()->get(ProductSellingDeniedRecalculator::class);
		/* @var $productSellingDeniedRecalculator \SS6\ShopBundle\Model\Product\ProductSellingDeniedRecalculator */

		$output->writeln('Running recalculations:');
		$output->writeln('<fg=green>Categories visibility.</fg=green>');
		$categoryVisibilityRepository->refreshCategoriesVisibility();

		$output->writeln('<fg=green>Products price.</fg=green>');
		$productPriceRecalculator->runAllScheduledRecalculations();

		$output->writeln('<fg=green>Products hiding.</fg=green>');
		$productHiddenRecalculator->calculateHiddenForAll();

		$output->writeln('<fg=green>Products visibility.</fg=green>');
		$productVisibilityFacade->refreshProductsVisibilityForMarked();

		$output->writeln('<fg=green>Products price again (because of variants).</fg=green>');
		// Main variant is set for recalculations after change of variants visibility.
		$productPriceRecalculator->runAllScheduledRecalculations();

		$output->writeln('<fg=green>Products availability.</fg=green>');
		$productAvailabilityRecalculator->runAllScheduledRecalculations();

		$output->writeln('<fg=green>Products selling denial.</fg=green>');
		$productSellingDeniedRecalculator->calculateSellingDeniedForAll();
	}

}
