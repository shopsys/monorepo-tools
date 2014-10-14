<?php

namespace SS6\ShopBundle\Model\Slider;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\FileUpload\EntityFileUploadInterface;
use SS6\ShopBundle\Model\FileUpload\FileForUpload;
use SS6\ShopBundle\Model\FileUpload\FileNamingConvention;
use SS6\ShopBundle\Model\Slider\SliderItemData;

/**
 * SliderItem
 *
 * @ORM\Table(name="slider_items")
 * @ORM\Entity
 */
class SliderItem implements EntityFileUploadInterface {

	/**
	 *
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 *
	 * @var string
	 * @ORM\Column(type="text")
	 */
	private $name;

	/**
	 *
	 * @var string
	 * @ORM\Column(type="text")
	 */
	private $link;

	/**
	 *
	 * @var string
	 * @ORM\Column(type="text")
	 */
	private $image;

	/**
	 * @var string
	 */
	private $imageForUpload;

	/**
	 * @param \SS6\ShopBundle\Model\Slider\SliderItemData $sliderItemData
	 */
	public function __construct(SliderItemData $sliderItemData) {
		$this->name = $sliderItemData->getName();
		$this->link = $sliderItemData->getLink();
		$this->setImageForUpload($sliderItemData->getImage());
	}

	/**
	 * @param \SS6\ShopBundle\Model\Slider\SliderItemData $sliderItemData
	 */
	public function edit(SliderItemData $sliderItemData) {
		$this->name = $sliderItemData->getName();
		$this->link = $sliderItemData->getLink();
		$this->setImageForUpload($sliderItemData->getImage());
	}

	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function getLink() {
		return $this->link;
	}

	public function getImage() {
		return $this->image;
	}

	public function getImageForUpload() {
		return $this->imageForUpload;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function setLink($link) {
		$this->link = $link;
	}

	public function setImage($image) {
		$this->image = $image;
	}

	public function setImageForUpload($imageForUpload) {
		$this->imageForUpload = $imageForUpload;
	}

	/**
	 * @return \SS6\ShopBundle\Model\FileUpload\FileForUpload[]
	 */
	public function getCachedFilesForUpload() {
		$files = array();
		if ($this->imageForUpload !== null) {
			$files['image'] = new FileForUpload($this->imageForUpload, true, 'sliderItem', 'default', FileNamingConvention::TYPE_ID);
		}
		return $files;
	}

	/**
	 * @param string $key
	 * @param string $originalFilename
	 */
	public function setFileAsUploaded($key, $originalFilename) {
		if ($key === 'image') {
			$this->image = pathinfo($originalFilename, PATHINFO_EXTENSION);
		} else {
			throw new \SS6\ShopBundle\Model\FileUpload\Exception\InvalidFileKeyException($key);
		}
	}

	/**
	 * @return string|null
	 */
	public function getImageFilename() {
		if ($this->image !== null) {
			return $this->getId() . '.' . $this->image;
		}

		return null;
	}

}
