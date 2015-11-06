<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Pricing\Vat\VatRepository;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityRepository;
use SS6\ShopBundle\Model\Product\Brand\BrandRepository;
use SS6\ShopBundle\Model\Product\Flag\FlagFacade;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\Unit\UnitFacade;

class ProductFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\VatRepository
	 */
	private $vatRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\AvailabilityRepository
	 */
	private $availabilityRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Brand\BrandRepository
	 */
	private $brandRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Flag\FlagFacade
	 */
	private $flagFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Unit\UnitFacade
	 */
	private $unitFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	public function __construct(
		VatRepository $vatRepository,
		AvailabilityRepository $availabilityRepository,
		BrandRepository $brandRepository,
		FlagFacade $flagFacade,
		UnitFacade $unitFacade,
		Domain $domain
	) {
		$this->vatRepository = $vatRepository;
		$this->availabilityRepository = $availabilityRepository;
		$this->brandRepository = $brandRepository;
		$this->flagFacade = $flagFacade;
		$this->unitFacade = $unitFacade;
		$this->domain = $domain;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product|null $product
	 * @return \SS6\ShopBundle\Form\Admin\Product\ProductFormType
	 */
	public function create(Product $product = null) {
		$vats = $this->vatRepository->getAllIncludingMarkedForDeletion();
		$availabilities = $this->availabilityRepository->getAll();
		$brands = $this->brandRepository->getAll();
		$flags = $this->flagFacade->getAll();
		$units = $this->unitFacade->getAll();

		return new ProductFormType(
			$vats,
			$availabilities,
			$brands,
			$flags,
			$units,
			$this->domain->getAll(),
			$product
		);
	}

}
