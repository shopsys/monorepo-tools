<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use SS6\ShopBundle\Model\FileUpload\FileUpload;
use SS6\ShopBundle\Model\Pricing\VatRepository;

class ProductFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\VatRepository
	 */
	private $vatRepository;

	/**
	 * @param \SS6\ShopBundle\Model\FileUpload\FileUpload $fileUpload
	 */
	public function __construct(FileUpload $fileUpload, VatRepository $vatRepository) {
		$this->fileUpload = $fileUpload;
		$this->vatRepository = $vatRepository;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Product\ProductFormType
	 */
	public function create() {
		$vats = $this->vatRepository->findAll();

		return new ProductFormType($this->fileUpload, $vats);
	}

}
