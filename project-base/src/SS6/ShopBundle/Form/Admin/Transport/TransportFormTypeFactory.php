<?php

namespace SS6\ShopBundle\Form\Admin\Transport;

use SS6\ShopBundle\Model\FileUpload\FileUpload;

class TransportFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @param \SS6\ShopBundle\Model\FileUpload\FileUpload $fileUpload
	 */
	public function __construct(FileUpload $fileUpload) {
		$this->fileUpload = $fileUpload;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Transport\TransportFormType
	 */
	public function create() {
		return new TransportFormType($this->fileUpload);
	}

}
