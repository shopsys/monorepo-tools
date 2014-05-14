<?php

namespace SS6\ShopBundle\Model\FileUpload;

class FileForUpload {

	/**
	 * @var string
	 */
	private $cacheFilename;

	/**
	 * @var bool
	 */
	private $isImage;

	/**
	 * @var string
	 */
	private $category;

	/**
	 * @var int
	 */
	private $nameConventionType;

	/**
	 * @param string $cacheFilename
	 * @param bool $isImage
	 * @param string $category
	 * @param int $nameConventionType
	 */
	public function __construct($cacheFilename, $isImage, $category, $nameConventionType) {
		$this->cacheFilename = $cacheFilename;
		$this->isImage = $isImage;
		$this->category = $category;
		$this->nameConventionType = $nameConventionType;
	}

	/**
	 * @return string
	 */
	public function getCacheFilename() {
		return $this->cacheFilename;
	}

	/**
	 * @return bool
	 */
	public function isImage() {
		return $this->isImage;
	}

	/**
	 * @return string
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * @return int
	 */
	public function getNameConventionType() {
		return $this->nameConventionType;
	}

}
