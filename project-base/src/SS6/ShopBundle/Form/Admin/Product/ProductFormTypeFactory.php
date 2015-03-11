<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use SS6\ShopBundle\Component\Transformers\InverseArrayValuesTransformer;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Pricing\Vat\VatRepository;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityRepository;
use SS6\ShopBundle\Model\Product\Flag\FlagRepository;

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
	 * @var \SS6\ShopBundle\Model\Product\InverseArrayValuesTransformer
	 */
	private $inverseArrayValuesTransformer;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Flag\FlagRepository
	 */
	private $flagRepository;

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	public function __construct(
		VatRepository $vatRepository,
		AvailabilityRepository $availabilityRepository,
		InverseArrayValuesTransformer $inverseArrayValuesTransformer,
		FlagRepository $flagRepository,
		Translator $translator
	) {
		$this->vatRepository = $vatRepository;
		$this->availabilityRepository = $availabilityRepository;
		$this->inverseArrayValuesTransformer = $inverseArrayValuesTransformer;
		$this->flagRepository = $flagRepository;
		$this->translator = $translator;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Product\ProductFormType
	 */
	public function create() {
		$vats = $this->vatRepository->findAll();
		$availabilities = $this->availabilityRepository->getAll();
		$flags = $this->flagRepository->findAll();

		return new ProductFormType(
			$vats,
			$availabilities,
			$this->inverseArrayValuesTransformer,
			$flags,
			$this->translator
		);
	}

}
