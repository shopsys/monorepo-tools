<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use SS6\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormTypeFactory;
use SS6\ShopBundle\Model\Department\DepartmentRepository;
use SS6\ShopBundle\Model\FileUpload\FileUpload;
use SS6\ShopBundle\Model\Image\ImageFacade;
use SS6\ShopBundle\Model\Pricing\Vat\VatRepository;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityRepository;
use SS6\ShopBundle\Model\Product\Product;
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
	 * @var \SS6\ShopBundle\Model\Department\DepartmentRepository
	 */
	private $departmentRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageFacade
	 */
	private $imageFacade;

	public function __construct(
		FileUpload $fileUpload,
		VatRepository $vatRepository,
		AvailabilityRepository $availabilityRepository,
		ProductParameterValueFormTypeFactory $productParameterValueFormTypeFactory,
		InverseArrayValuesTransformer $inverseArrayValuesTransformer,
		DepartmentRepository $departmentRepository,
		ImageFacade $imageFacade
	) {
		$this->fileUpload = $fileUpload;
		$this->vatRepository = $vatRepository;
		$this->availabilityRepository = $availabilityRepository;
		$this->productParameterValueFormTypeFactory = $productParameterValueFormTypeFactory;
		$this->inverseArrayValuesTransformer = $inverseArrayValuesTransformer;
		$this->departmentRepository = $departmentRepository;
		$this->imageFacade = $imageFacade;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product|null $product
	 * @return \SS6\ShopBundle\Form\Admin\Product\ProductFormType
	 */
	public function create(Product $product = null) {
		$vats = $this->vatRepository->findAll();
		$availabilities = $this->availabilityRepository->findAll();
		$departments = $this->departmentRepository->getAll();

		if ($product !== null) {
			$images = $this->imageFacade->getImagesByEntity($product, null);
		} else {
			$images = array();
		}

		return new ProductFormType(
			$this->fileUpload,
			$vats,
			$availabilities,
			$this->productParameterValueFormTypeFactory,
			$this->inverseArrayValuesTransformer,
			$departments,
			$images
		);
	}

}
