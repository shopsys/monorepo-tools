<?php

namespace SS6\ShopBundle\Component\DataFixture;

use Doctrine\Common\DataFixtures\ReferenceRepository;
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
	 * @param \Doctrine\Common\DataFixtures\ReferenceRepository $referenceRepository
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function loadReferences(
		ProductDataFixtureLoader $productDataFixtureLoader,
		ReferenceRepository $referenceRepository
	) {
		$vats = $this->getVatReferences($referenceRepository);
		$availabilities = $this->getAvailabilityReferences($referenceRepository);
		$categories = $this->getCategoryReferences($referenceRepository);
		$flags = $this->getFlagReferences($referenceRepository);
		$brands = $this->getBrandReferences($referenceRepository);
		$units = $this->getUnitReferences($referenceRepository);
		$pricingGroups = $this->getPricingGroupReferences($referenceRepository);

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
	 * @param \Doctrine\Common\DataFixtures\ReferenceRepository $referenceRepository
	 * @return string[]
	 */
	private function getVatReferences(ReferenceRepository $referenceRepository) {
		return [
			'high' => $referenceRepository->getReference(VatDataFixture::VAT_HIGH),
			'low' => $referenceRepository->getReference(VatDataFixture::VAT_LOW),
			'second_low' => $referenceRepository->getReference(VatDataFixture::VAT_SECOND_LOW),
			'zero' => $referenceRepository->getReference(VatDataFixture::VAT_ZERO),
		];
	}

	/**
	 * @param \Doctrine\Common\DataFixtures\ReferenceRepository $referenceRepository
	 * @return string[]
	 */
	private function getAvailabilityReferences(ReferenceRepository $referenceRepository) {
		return [
			'in-stock' => $referenceRepository->getReference(AvailabilityDataFixture::IN_STOCK),
			'out-of-stock' => $referenceRepository->getReference(AvailabilityDataFixture::OUT_OF_STOCK),
			'on-request' => $referenceRepository->getReference(AvailabilityDataFixture::ON_REQUEST),
		];
	}

	/**
	 * @param \Doctrine\Common\DataFixtures\ReferenceRepository $referenceRepository
	 * @return string[]
	 */
	private function getCategoryReferences(ReferenceRepository $referenceRepository) {
		return [
			CategoryDataFixture::TV => $referenceRepository->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::TV
			),
			CategoryDataFixture::PHOTO => $referenceRepository->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::PHOTO
			),
			CategoryDataFixture::PRINTERS => $referenceRepository->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::PRINTERS
			),
			CategoryDataFixture::PC => $referenceRepository->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::PC
			),
			CategoryDataFixture::PHONES => $referenceRepository->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::PHONES
			),
			CategoryDataFixture::COFFEE => $referenceRepository->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::COFFEE
			),
			CategoryDataFixture::BOOKS => $referenceRepository->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::BOOKS
			),
			CategoryDataFixture::TOYS => $referenceRepository->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::TOYS
			),
			CategoryDataFixture::GARDEN_TOOLS => $referenceRepository->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::GARDEN_TOOLS
			),
			CategoryDataFixture::FOOD => $referenceRepository->getReference(
				CategoryDataFixture::PREFIX . CategoryDataFixture::FOOD
			),
		];
	}

	/**
	 * @param \Doctrine\Common\DataFixtures\ReferenceRepository $referenceRepository
	 * @return string[]
	 */
	private function getFlagReferences(ReferenceRepository $referenceRepository) {
		return [
			'action' => $referenceRepository->getReference(FlagDataFixture::ACTION_PRODUCT),
			'new' => $referenceRepository->getReference(FlagDataFixture::NEW_PRODUCT),
			'top' => $referenceRepository->getReference(FlagDataFixture::TOP_PRODUCT),
		];
	}

	/**
	 * @param \Doctrine\Common\DataFixtures\ReferenceRepository $referenceRepository
	 * @return string[]
	 */
	private function getBrandReferences(ReferenceRepository $referenceRepository) {
		return [
			'apple' => $referenceRepository->getReference(BrandDataFixture::APPLE),
			'canon' => $referenceRepository->getReference(BrandDataFixture::CANON),
			'lg' => $referenceRepository->getReference(BrandDataFixture::LG),
			'philips' => $referenceRepository->getReference(BrandDataFixture::PHILIPS),
			'sencor' => $referenceRepository->getReference(BrandDataFixture::SENCOR),
			'a4tech' => $referenceRepository->getReference(BrandDataFixture::A4TECH),
			'brother' => $referenceRepository->getReference(BrandDataFixture::BROTHER),
			'verbatim' => $referenceRepository->getReference(BrandDataFixture::VERBATIM),
			'dlink' => $referenceRepository->getReference(BrandDataFixture::DLINK),
			'defender' => $referenceRepository->getReference(BrandDataFixture::DEFENDER),
			'delonghi' => $referenceRepository->getReference(BrandDataFixture::DELONGHI),
			'genius' => $referenceRepository->getReference(BrandDataFixture::GENIUS),
			'gigabyte' => $referenceRepository->getReference(BrandDataFixture::GIGABYTE),
			'hp' => $referenceRepository->getReference(BrandDataFixture::HP),
			'htc' => $referenceRepository->getReference(BrandDataFixture::HTC),
			'jura' => $referenceRepository->getReference(BrandDataFixture::JURA),
			'logitech' => $referenceRepository->getReference(BrandDataFixture::LOGITECH),
			'microsoft' => $referenceRepository->getReference(BrandDataFixture::MICROSOFT),
			'samsung' => $referenceRepository->getReference(BrandDataFixture::SAMSUNG),
			'sony' => $referenceRepository->getReference(BrandDataFixture::SONY),
			'orava' => $referenceRepository->getReference(BrandDataFixture::ORAVA),
			'olympus' => $referenceRepository->getReference(BrandDataFixture::OLYMPUS),
			'hyundai' => $referenceRepository->getReference(BrandDataFixture::HYUNDAI),
			'nikon' => $referenceRepository->getReference(BrandDataFixture::NIKON),
		];
	}

	/**
	 * @param \Doctrine\Common\DataFixtures\ReferenceRepository $referenceRepository
	 * @return string[]
	 */
	private function getUnitReferences(ReferenceRepository $referenceRepository) {
		return [
			'pcs' => $referenceRepository->getReference(BaseUnitDataFixture::PCS),
			'm3' => $referenceRepository->getReference(DemoUnitDataFixture::M3),
		];
	}

	/**
	 * @param \Doctrine\Common\DataFixtures\ReferenceRepository $referenceRepository
	 * @return string[]
	 */
	private function getPricingGroupReferences(ReferenceRepository $referenceRepository) {
		return [
			'ordinary_domain_1' => $referenceRepository->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1),
			'ordinary_domain_2' => $referenceRepository->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_2),
			'partner_domain_1' => $referenceRepository->getReference(PricingGroupDataFixture::PARTNER_DOMAIN_1),
			'vip_domain_1' => $referenceRepository->getReference(PricingGroupDataFixture::VIP_DOMAIN_1),
			'vip_domain_2' => $referenceRepository->getReference(PricingGroupDataFixture::VIP_DOMAIN_2),
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
