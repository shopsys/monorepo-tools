<?php

namespace SS6\ShopBundle\Model\Pricing\Vat;

use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;

class VatService {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
	 */
	private $productPriceRecalculationScheduler;

	public function __construct(ProductPriceRecalculationScheduler $productPriceRecalculationScheduler) {
		$this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\VatData $vatData
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public function create(VatData $vatData) {
		return new Vat($vatData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\VatData $vatData
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public function edit(Vat $vat, VatData $vatData) {
		$vat->edit($vatData);
		$this->productPriceRecalculationScheduler->scheduleRecalculatePriceForVat($vat);

		return $vat;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $defaultVat
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vatToDelete
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $newVat
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public function getNewDefaultVat(Vat $defaultVat, Vat $vatToDelete, Vat $newVat) {
		if ($defaultVat->getId() === $vatToDelete->getId()) {
			return $newVat;
		}
		return $defaultVat;
	}

}
