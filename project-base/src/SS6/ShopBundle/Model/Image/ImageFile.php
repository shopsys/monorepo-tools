<?php

namespace SS6\ShopBundle\Model\Image;

class ImageFile {

	/**
	 * @var string
	 */
	private $category;

	/**
	 * @var string|null
	 */
	private $type;

	/**
	 * @var string
	 */
	private $filename;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @param string $category
	 * @param string|null $type
	 * @param string $filename
	 */
	function __construct($category, $type, $filename, $title = null) {
		$this->category = $category;
		$this->type = $type;
		$this->filename = $filename;
		$this->title = $title;
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
	 * @return string
	 */
	public function getFilename() {
		return $this->filename;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title ?: $this->filename;
	}

}
