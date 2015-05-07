<?php

namespace SS6\ShopBundle\Command;

use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronCommand extends ContainerAwareCommand {

	const PRODUCTS_PRICES_RECALCULATIONS_TIMELIMIT = 20;
	const PRODUCTS_AVAILABILITY_RECALCULATIONS_TIMELIMIT = 20;

	protected function configure() {
		$this
			->setName('ss6:cron')
			->setDescription('Maintenance service of ShopSys 6');
	}

	/**
	 * @param \Symfony\Component\Console\Input\InputInterface $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->recalculateProductsPrices($output);
		$this->recalculateProductsAvailability($output);
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	private function recalculateProductsPrices(OutputInterface $output) {
		$output->writeln('Product price recalculation');

		$productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */
		$timeStart = time();
		$recalculatedCount = $productPriceRecalculator->runScheduledRecalculations(function () use ($timeStart) {
			return time() - $timeStart  < self::PRODUCTS_PRICES_RECALCULATIONS_TIMELIMIT;
		});

		$output->writeln('Recalculated: ' . $recalculatedCount);
	}

	/**
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	private function recalculateProductsAvailability(OutputInterface $output) {
		$output->writeln('Product availability recalculation');

		$productAvailabilityRecalculator = $this->getContainer()->get(ProductAvailabilityRecalculator::class);
		/* @var $productAvailabilityRecalculator \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator */
		$timeStart = time();
		$recalculatedCount = $productAvailabilityRecalculator->runScheduledRecalculations(function () use ($timeStart) {
			return time() - $timeStart  < self::PRODUCTS_PRICES_RECALCULATIONS_TIMELIMIT;
		});

		$output->writeln('Recalculated: ' . $recalculatedCount);
	}

}
