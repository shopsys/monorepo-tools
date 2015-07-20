<?php

namespace SS6\ShopBundle\Component\DataFixture;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use SS6\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use SS6\ShopBundle\DataFixtures\Base\FlagDataFixture;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\BrandDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader;

class ProductDataFixtureReferenceInjector {

	/**
	 * @param \SS6\ShopBundle\DataFixtures\Demo\ProductDataFixtureLoader $productDataFixtureLoader
	 * @param \Doctrine\Common\DataFixtures\ReferenceRepository $referenceRepository
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
			'1' => $referenceRepository->getReference(CategoryDataFixture::TV),
			'2' => $referenceRepository->getReference(CategoryDataFixture::PHOTO),
			'3' => $referenceRepository->getReference(CategoryDataFixture::PRINTERS),
			'4' => $referenceRepository->getReference(CategoryDataFixture::PC),
			'5' => $referenceRepository->getReference(CategoryDataFixture::PHONES),
			'6' => $referenceRepository->getReference(CategoryDataFixture::COFFEE),
			'7' => $referenceRepository->getReference(CategoryDataFixture::BOOKS),
			'8' => $referenceRepository->getReference(CategoryDataFixture::TOYS),
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

		$productDataFixtureLoader->injectReferences($vats, $availabilities, $categories, $flags, $brands);
	}

	/**
	 * @return string[]
	 */
	public static function getDependencies() {
		return [
			VatDataFixture::class,
			AvailabilityDataFixture::class,
			CategoryDataFixture::class,
			BrandDataFixture::class,
		];
	}

}
