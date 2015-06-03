<?php

namespace SS6\ShopBundle\Model\Pricing\Vat;

use SS6\ShopBundle\Component\Cron\CronServiceInterface;
use SS6\ShopBundle\Model\Pricing\Vat\VatFacade;
use SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceFacade;
use Symfony\Bridge\Monolog\Logger;

class VatDeletionCronService implements CronServiceInterface {

	const PRODUCTS_REPLACE_VAT_TIMELIMIT = 240;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\VatFacade
	 */
	private $vatFacade;

	/**
	 * @var ProductInputPriceFacade
	 */
	private $productInputPriceFacade;

	public function __construct(VatFacade $vatFacade, ProductInputPriceFacade $productInputPriceFacade) {
		$this->vatFacade = $vatFacade;
		$this->productInputPriceFacade = $productInputPriceFacade;
	}

	/**
	 * @inheritDoc
	 */
	public function run(Logger $logger) {
		$timeStart = time();

		$recalculatedCount = $this->productInputPriceFacade->replaceVatAndRecalculateInputPrices(function () use ($timeStart) {
			return time() - $timeStart < self::PRODUCTS_REPLACE_VAT_TIMELIMIT;
		});
		$logger->addInfo('Recalculated ' . $recalculatedCount . ' products.');

		$deletedVats = $this->vatFacade->deleteAllReplacedVats();
		$logger->addInfo('Deleted ' . $deletedVats . ' vats.');
	}
}
