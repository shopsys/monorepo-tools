<?php

namespace SS6\ShopBundle\Component\DataFixture;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use SS6\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use SS6\ShopBundle\DataFixtures\Base\FlagDataFixture;
use SS6\ShopBundle\DataFixtures\Base\FulltextTriggersDataFixture;
use SS6\ShopBundle\DataFixtures\Base\UnitDataFixture;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\BrandDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader;

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
		$vats = [
			'high' => $referenceRepository->getReference(VatDataFixture::VAT_HIGH),
			'low' => $referenceRepository->getReference(VatDataFixture::VAT_LOW),
			'zero' => $referenceRepository->getReference(VatDataFixture::VAT_ZERO),
		];

		$availabilities = [
			'in-stock' => $referenceRepository->getReference(AvailabilityDataFixture::IN_STOCK),
			'out-of-stock' => $referenceRepository->getReference(AvailabilityDataFixture::OUT_OF_STOCK),
			'on-request' => $referenceRepository->getReference(AvailabilityDataFixture::ON_REQUEST),
		];

		$categories = [
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
		];

		$flags = [
			'action' => $referenceRepository->getReference(FlagDataFixture::ACTION_PRODUCT),
			'new' => $referenceRepository->getReference(FlagDataFixture::NEW_PRODUCT),
			'top' => $referenceRepository->getReference(FlagDataFixture::TOP_PRODUCT),
		];

		$brands = [
			'apple' => $referenceRepository->getReference(BrandDataFixture::APPLE),
			'canon' => $referenceRepository->getReference(BrandDataFixture::CANON),
			'lg' => $referenceRepository->getReference(BrandDataFixture::LG),
			'philips' => $referenceRepository->getReference(BrandDataFixture::PHILIPS),
		];

		$units = [
			'pcs' => $referenceRepository->getReference(UnitDataFixture::PCS),
		];

		$productDataFixtureLoader->injectReferences(
			$vats,
			$availabilities,
			$categories,
			$flags,
			$brands,
			$units
		);
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
		];
	}

}
