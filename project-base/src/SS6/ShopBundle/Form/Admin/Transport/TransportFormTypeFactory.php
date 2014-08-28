<?php

namespace SS6\ShopBundle\Form\Admin\Transport;

use SS6\ShopBundle\Model\FileUpload\FileUpload;
use SS6\ShopBundle\Model\Pricing\Vat\VatRepository;

class TransportFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\VatRepository
	 */
	private $vatRepository;

	/**
	 * @param \SS6\ShopBundle\Model\FileUpload\FileUpload $fileUpload
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\VatRepository $vatRepository
	 */
	public function __construct(FileUpload $fileUpload, VatRepository $vatRepository) {
		$this->fileUpload = $fileUpload;
		$this->vatRepository = $vatRepository;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Transport\TransportFormType
	 */
	public function create() {
		$vats = $this->vatRepository->findAll();

		return new TransportFormType($this->fileUpload, $vats);
	}

}
