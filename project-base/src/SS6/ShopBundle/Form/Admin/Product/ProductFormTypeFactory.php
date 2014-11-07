<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use SS6\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormTypeFactory;
use SS6\ShopBundle\Model\FileUpload\FileUpload;
use SS6\ShopBundle\Model\Pricing\Vat\VatRepository;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityRepository;
use SS6\ShopBundle\Component\Transformers\InverseArrayValuesTransformer;

class ProductFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\VatRepository
	 */
	private $vatRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\AvailabilityRepository
	 */
	private $availabilityRepository;

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormTypeFactory
	 */
	private $productParameterValueFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Product\InverseArrayValuesTransformer
	 */
	private $inverseArrayValuesTransformer;

	/**
	 * @param \SS6\ShopBundle\Model\FileUpload\FileUpload $fileUpload
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\VatRepository $vatRepository
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityRepository $availabilityRepository
	 * @param \SS6\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormTypeFactory $productParameterValueFormTypeFactory
	 * @param \SS6\ShopBundle\Model\Product\InverseArrayValuesTransformer $inverseArrayValuesTransformer
	 */
	public function __construct(
		FileUpload $fileUpload,
		VatRepository $vatRepository,
		AvailabilityRepository $availabilityRepository,
		ProductParameterValueFormTypeFactory $productParameterValueFormTypeFactory,
		InverseArrayValuesTransformer $inverseArrayValuesTransformer
	) {
		$this->fileUpload = $fileUpload;
		$this->vatRepository = $vatRepository;
		$this->availabilityRepository = $availabilityRepository;
		$this->productParameterValueFormTypeFactory = $productParameterValueFormTypeFactory;
		$this->inverseArrayValuesTransformer = $inverseArrayValuesTransformer;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Product\ProductFormType
	 */
	public function create() {
		$vats = $this->vatRepository->findAll();
		$availabilities = $this->availabilityRepository->findAll();

		return new ProductFormType(
			$this->fileUpload,
			$vats,
			$availabilities,
			$this->productParameterValueFormTypeFactory,
			$this->inverseArrayValuesTransformer
		);
	}

}
