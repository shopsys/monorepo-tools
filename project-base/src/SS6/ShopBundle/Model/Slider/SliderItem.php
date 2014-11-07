<?php

namespace SS6\ShopBundle\Model\Slider;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\FileUpload\EntityFileUploadInterface;
use SS6\ShopBundle\Model\FileUpload\FileForUpload;
use SS6\ShopBundle\Model\FileUpload\FileNamingConvention;
use SS6\ShopBundle\Model\Grid\Ordering\OrderableEntityInterface;
use SS6\ShopBundle\Model\Slider\SliderItemData;

/**
 * SliderItem
 *
 * @ORM\Table(name="slider_items")
 * @ORM\Entity
 */
class SliderItem implements EntityFileUploadInterface, OrderableEntityInterface {

	/**
	 * @var int
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text")
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text")
	 */
	private $link;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text")
	 */
	private $image;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 */
	private $domainId;

	/**
	 * @var int|null
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $position;

	/**
	 * @var string
	 */
	private $imageForUpload;

	/**
	 * @param \SS6\ShopBundle\Model\Slider\SliderItemData $sliderItemData
	 */
	public function __construct(SliderItemData $sliderItemData, $domainId) {
		$this->domainId = $domainId;
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

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getLink() {
		return $this->link;
	}

	/**
	 * @return string
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * @return string
	 */
	public function getImageForUpload() {
		return $this->imageForUpload;
	}

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

	/**
	 * @param string $image
	 */
	public function setImage($image) {
		$this->image = $image;
	}

	/**
	 * @param string $imageForUpload
	 */
	public function setImageForUpload($imageForUpload) {
		$this->imageForUpload = $imageForUpload;
	}

	/**
	 * @return \SS6\ShopBundle\Model\FileUpload\FileForUpload[]
	 */
	public function getCachedFilesForUpload() {
		$files = array();
		if ($this->imageForUpload !== null) {
			$files['image'] = new FileForUpload($this->imageForUpload, true, 'sliderItem', null, FileNamingConvention::TYPE_ID);
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

	/**
	 * @return int|null
	 */
	public function getPosition() {
		return $this->position;
	}

	/**
	 * @param int $position
	 */
	public function setPosition($position) {
		$this->position = $position;
	}



}
