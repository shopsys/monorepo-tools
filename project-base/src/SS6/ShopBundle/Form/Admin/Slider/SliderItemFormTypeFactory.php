<?php

namespace SS6\ShopBundle\Form\Admin\Slider;

use SS6\ShopBundle\Form\Admin\Slider\SliderItemFormType;
use SS6\ShopBundle\Model\FileUpload\FileUpload;

class SliderItemFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @param \SS6\ShopBundle\Form\Admin\Slider\FileUpload $fileUpload
	 */
	public function __construct(FileUpload $fileUpload) {
		$this->fileUpload = $fileUpload;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Slider\SliderItemFormType
	 */
	public function create() {
		return new SliderItemFormType($this->fileUpload);
	}
}
