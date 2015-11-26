<?php

namespace SS6\ShopBundle\Component\DataFixture;

use SS6\ShopBundle\Component\DataFixture\PersistentReferenceService;
use SS6\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use SS6\ShopBundle\DataFixtures\Base\FlagDataFixture;
use SS6\ShopBundle\DataFixtures\Base\FulltextTriggersDataFixture;
use SS6\ShopBundle\DataFixtures\Base\PricingGroupDataFixture;
use SS6\ShopBundle\DataFixtures\Base\UnitDataFixture as BaseUnitDataFixture;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\BrandDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader;
use SS6\ShopBundle\DataFixtures\Demo\UnitDataFixture as DemoUnitDataFixture;

class ProductDataFixtureReferenceInjector {

	/**
	 * @param \SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader $productDataFixtureLoader
	 * @param \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService $persistentReferenceService
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function loadReferences(
		ProductDataFixtureLoader $productDataFixtureLoader,
		PersistentReferenceService $persistentReferenceService
	) {
		$vats = $this->getVatReferences($persistentReferenceService);
		$availabilities = $this->getAvailabilityReferences($persistentReferenceService);
		$categories = $this->getCategoryReferences($persistentReferenceService);
		$flags = $this->getFlagReferences($persistentReferenceService);
		$brands = $this->getBrandReferences($persistentReferenceService);
		$units = $this->getUnitReferences($persistentReferenceService);
		$pricingGroups = $this->getPricingGroupReferences($persistentReferenceService);

		$productDataFixtureLoader->injectReferences(
			$vats,
			$availabilities,
			$categories,
			$flags,
			$brands,
			$units,
			$pricingGroups
		);
	}

	/**
	 * @param \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService $persistentReferenceService
	 * @return string[]
	 */
	private function getVatReferences(PersistentReferenceService $persistentReferenceService) {
		return [
			'high' => $persistentReferenceService->getReference(VatDataFixture::VAT_HIGH),
			'low' => $persistentReferenceService->getReference(VatDataFixture::VAT_LOW),
			'second_low' => $persistentReferenceService->getReference(VatDataFixture::VAT_SECOND_LOW),
			'zero' => $persistentReferenceService->getReference(VatDataFixture::VAT_ZERO),
		];
	}

	/**
	 * @param \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService $persistentReferenceService
	 * @return string[]
	 */
	private function getAvailabilityReferences(PersistentReferenceService $persistentReferenceService) {
		return [
			'in-stock' => $persistentReferenceService->getReference(AvailabilityDataFixture::IN_STOCK),
			'out-of-stock' => $persistentReferenceService->getReference(AvailabilityDataFixture::OUT_OF_STOCK),
			'on-request' => $persistentReferenceService->getReference(AvailabilityDataFixture::ON_REQUEST),
		];
	}

	/**
	 * @param \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService $persistentReferenceService
	 * @return string[]
	 */
	private function getCategoryReferences(PersistentReferenceService $persistentReferenceService) {
		return [
			CategoryDataFixture::TV => $persistentReferenceService->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::TV
			),
			CategoryDataFixture::PHOTO => $persistentReferenceService->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::PHOTO
			),
			CategoryDataFixture::PRINTERS => $persistentReferenceService->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::PRINTERS
			),
			CategoryDataFixture::PC => $persistentReferenceService->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::PC
			),
			CategoryDataFixture::PHONES => $persistentReferenceService->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::PHONES
			),
			CategoryDataFixture::COFFEE => $persistentReferenceService->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::COFFEE
			),
			CategoryDataFixture::BOOKS => $persistentReferenceService->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::BOOKS
			),
			CategoryDataFixture::TOYS => $persistentReferenceService->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::TOYS
			),
			CategoryDataFixture::GARDEN_TOOLS => $persistentReferenceService->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::GARDEN_TOOLS
			),
			CategoryDataFixture::FOOD => $persistentReferenceService->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::FOOD
			),
		];
	}

	/**
	 * @param \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService $persistentReferenceService
	 * @return string[]
	 */
	private function getFlagReferences(PersistentReferenceService $persistentReferenceService) {
		return [
			'action' => $persistentReferenceService->getReference(FlagDataFixture::ACTION_PRODUCT),
			'new' => $persistentReferenceService->getReference(FlagDataFixture::NEW_PRODUCT),
			'top' => $persistentReferenceService->getReference(FlagDataFixture::TOP_PRODUCT),
		];
	}

	/**
	 * @param \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService $persistentReferenceService
	 * @return string[]
	 */
	private function getBrandReferences(PersistentReferenceService $persistentReferenceService) {
		return [
			'apple' => $persistentReferenceService->getReference(BrandDataFixture::APPLE),
			'canon' => $persistentReferenceService->getReference(BrandDataFixture::CANON),
			'lg' => $persistentReferenceService->getReference(BrandDataFixture::LG),
			'philips' => $persistentReferenceService->getReference(BrandDataFixture::PHILIPS),
			'sencor' => $persistentReferenceService->getReference(BrandDataFixture::SENCOR),
			'a4tech' => $persistentReferenceService->getReference(BrandDataFixture::A4TECH),
			'brother' => $persistentReferenceService->getReference(BrandDataFixture::BROTHER),
			'verbatim' => $persistentReferenceService->getReference(BrandDataFixture::VERBATIM),
			'dlink' => $persistentReferenceService->getReference(BrandDataFixture::DLINK),
			'defender' => $persistentReferenceService->getReference(BrandDataFixture::DEFENDER),
			'delonghi' => $persistentReferenceService->getReference(BrandDataFixture::DELONGHI),
			'genius' => $persistentReferenceService->getReference(BrandDataFixture::GENIUS),
			'gigabyte' => $persistentReferenceService->getReference(BrandDataFixture::GIGABYTE),
			'hp' => $persistentReferenceService->getReference(BrandDataFixture::HP),
			'htc' => $persistentReferenceService->getReference(BrandDataFixture::HTC),
			'jura' => $persistentReferenceService->getReference(BrandDataFixture::JURA),
			'logitech' => $persistentReferenceService->getReference(BrandDataFixture::LOGITECH),
			'microsoft' => $persistentReferenceService->getReference(BrandDataFixture::MICROSOFT),
			'samsung' => $persistentReferenceService->getReference(BrandDataFixture::SAMSUNG),
			'sony' => $persistentReferenceService->getReference(BrandDataFixture::SONY),
			'orava' => $persistentReferenceService->getReference(BrandDataFixture::ORAVA),
			'olympus' => $persistentReferenceService->getReference(BrandDataFixture::OLYMPUS),
			'hyundai' => $persistentReferenceService->getReference(BrandDataFixture::HYUNDAI),
			'nikon' => $persistentReferenceService->getReference(BrandDataFixture::NIKON),
		];
	}

	/**
	 * @param \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService $persistentReferenceService
	 * @return string[]
	 */
	private function getUnitReferences(PersistentReferenceService $persistentReferenceService) {
		return [
			'pcs' => $persistentReferenceService->getReference(BaseUnitDataFixture::PCS),
			'm3' => $persistentReferenceService->getReference(DemoUnitDataFixture::M3),
		];
	}

	/**
	 * @param \SS6\ShopBundle\Component\DataFixture\PersistentReferenceService $persistentReferenceService
	 * @return string[]
	 */
	private function getPricingGroupReferences(PersistentReferenceService $persistentReferenceService) {
		return [
			'ordinary_domain_1' => $persistentReferenceService->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1),
			'ordinary_domain_2' => $persistentReferenceService->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_2),
			'partner_domain_1' => $persistentReferenceService->getReference(PricingGroupDataFixture::PARTNER_DOMAIN_1),
			'vip_domain_1' => $persistentReferenceService->getReference(PricingGroupDataFixture::VIP_DOMAIN_1),
			'vip_domain_2' => $persistentReferenceService->getReference(PricingGroupDataFixture::VIP_DOMAIN_2),
		];
	}

	/**
	 * @return string[]
	 */
	public static function getDependencies() {
		return [
			FulltextTriggersDataFixture::class,
			VatDataFixture::class,
			AvailabilityDataFixture::class,
			CategoryDataFixture::class,
			BrandDataFixture::class,
			BaseUnitDataFixture::class,
			DemoUnitDataFixture::class,
			PricingGroupDataFixture::class,
		];
	}

}
