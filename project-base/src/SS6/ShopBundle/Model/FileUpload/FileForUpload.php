<?php

namespace SS6\ShopBundle\Model\FileUpload;

class FileForUpload {

	/**
	 * @var string
	 */
	private $temporaryFilename;

	/**
	 * @var bool
	 */
	private $isImage;

	/**
	 * @var string
	 */
	private $category;

	/**
	 * @var string|null
	 */
	private $type;

	/**
	 * @var int
	 */
	private $nameConventionType;

	/**
	 * @param string $temporaryFilename
	 * @param bool $isImage
	 * @param string $category
	 * @param string|null $type
	 * @param int $nameConventionType
	 */
	public function __construct($temporaryFilename, $isImage, $category, $type, $nameConventionType) {
		$this->temporaryFilename = $temporaryFilename;
		$this->isImage = $isImage;
		$this->category = $category;
		$this->type = $type;
		$this->nameConventionType = $nameConventionType;
	}

	/**
	 * @return string
	 */
	public function getTemporaryFilename() {
		return $this->temporaryFilename;
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
	 * @return string|null
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return int
	 */
	public function getNameConventionType() {
		return $this->nameConventionType;
	}

}
