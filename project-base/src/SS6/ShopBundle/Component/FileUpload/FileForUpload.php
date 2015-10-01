<?php

namespace SS6\ShopBundle\Component\FileUpload;

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
	private $targetDirectory;

	/**
	 * @var int
	 */
	private $nameConventionType;

	/**
	 * @param string $temporaryFilename
	 * @param bool $isImage
	 * @param string $category
	 * @param string|null $targetDirectory
	 * @param int $nameConventionType
	 */
	public function __construct($temporaryFilename, $isImage, $category, $targetDirectory, $nameConventionType) {
		$this->temporaryFilename = $temporaryFilename;
		$this->isImage = $isImage;
		$this->category = $category;
		$this->targetDirectory = $targetDirectory;
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
	public function getTargetDirectory() {
		return $this->targetDirectory;
	}

	/**
	 * @return int
	 */
	public function getNameConventionType() {
		return $this->nameConventionType;
	}

}
