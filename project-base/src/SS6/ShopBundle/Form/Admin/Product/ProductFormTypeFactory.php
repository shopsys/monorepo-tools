<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use SS6\ShopBundle\Component\Transformers\InverseArrayValuesTransformer;
use SS6\ShopBundle\Model\Category\CategoryRepository;
use SS6\ShopBundle\Model\Pricing\Vat\VatRepository;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityRepository;
use SS6\ShopBundle\Model\Product\Flag\FlagRepository;
use Symfony\Component\Translation\TranslatorInterface;

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
	 * @var \SS6\ShopBundle\Model\Category\CategoryRepository
	 */
	private $categoryRepository;

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
		CategoryRepository $categoryRepository,
		FlagRepository $flagRepository,
		TranslatorInterface $translator
	) {
		$this->vatRepository = $vatRepository;
		$this->availabilityRepository = $availabilityRepository;
		$this->inverseArrayValuesTransformer = $inverseArrayValuesTransformer;
		$this->categoryRepository = $categoryRepository;
		$this->flagRepository = $flagRepository;
		$this->translator = $translator;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Product\ProductFormType
	 */
	public function create() {
		$vats = $this->vatRepository->findAll();
		$availabilities = $this->availabilityRepository->getAll();
		$categories = $this->categoryRepository->getAll();
		$flags = $this->flagRepository->findAll();

		return new ProductFormType(
			$vats,
			$availabilities,
			$this->inverseArrayValuesTransformer,
			$categories,
			$flags,
			$this->translator
		);
	}

}
