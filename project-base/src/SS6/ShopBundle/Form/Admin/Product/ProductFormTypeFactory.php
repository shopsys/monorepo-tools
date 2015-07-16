<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use SS6\ShopBundle\Component\Transformers\InverseArrayValuesTransformer;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Pricing\Vat\VatRepository;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityRepository;
use SS6\ShopBundle\Model\Product\Brand\BrandRepository;
use SS6\ShopBundle\Model\Product\Flag\FlagFacade;
use SS6\ShopBundle\Model\Product\Product;

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
	 * @var \SS6\ShopBundle\Component\Transformers\InverseArrayValuesTransformer
	 */
	private $inverseArrayValuesTransformer;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Flag\FlagFacade
	 */
	private $flagFacade;

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	public function __construct(
		VatRepository $vatRepository,
		AvailabilityRepository $availabilityRepository,
		BrandRepository $brandRepository,
		InverseArrayValuesTransformer $inverseArrayValuesTransformer,
		FlagFacade $flagFacade,
		Translator $translator
	) {
		$this->vatRepository = $vatRepository;
		$this->availabilityRepository = $availabilityRepository;
		$this->brandRepository = $brandRepository;
		$this->inverseArrayValuesTransformer = $inverseArrayValuesTransformer;
		$this->flagFacade = $flagFacade;
		$this->translator = $translator;
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

		return new ProductFormType(
			$vats,
			$availabilities,
			$brands,
			$this->inverseArrayValuesTransformer,
			$flags,
			$this->translator,
			$product
		);
	}

}
