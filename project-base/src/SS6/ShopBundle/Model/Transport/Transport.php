<?php

namespace SS6\ShopBundle\Model\Transport;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Image\EntityImageInterface;
use SS6\ShopBundle\Model\Image\ImageFileCollection;
use SS6\ShopBundle\Model\FileUpload\EntityFileUploadInterface;
use SS6\ShopBundle\Model\FileUpload\FileForUpload;
use SS6\ShopBundle\Model\FileUpload\FileNamingConvention;

/**
 * @ORM\Table(name="transports")
 * @ORM\Entity
 */
class Transport implements EntityFileUploadInterface, EntityImageInterface {

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6)
	 */
	private $price;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $description;

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $hidden;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $deleted;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=4, nullable=true)
	 */
	private $image;

	/**
	 * @var string|null
	 */
	private $imageForUpload;
	
	/**
	 * @param string $name
	 * @param string $price
	 * @param string|null $description
	 * @param boolean $hidden
	 */
	public function __construct($name, $price, $description = null, $hidden = false) {
		$this->name = $name;
		$this->price = $price;
		$this->description = $description;
		$this->hidden = $hidden;
		$this->deleted = false;
		$this->image = null;
	}
	
	/**
	 * @param string $name
	 * @param string $price
	 * @param string|null $description
	 * @param boolean $hidden
	 */
	public function setEdit($name, $price, $description, $hidden) {
		$this->name = $name;
		$this->price = $price;
		$this->description = $description;
		$this->hidden = $hidden;
	}

	/**
	 * @return \SS6\ShopBundle\Model\FileUpload\FileForUpload[]
	 */
	public function getCachedFilesForUpload() {
		$files = array();
		if ($this->imageForUpload !== null) {
			$files['image'] = new FileForUpload($this->imageForUpload, true, 'transport', null, FileNamingConvention::TYPE_ID);
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
	 * @return \SS6\ShopBundle\Model\Image\ImageFileCollection
	 */
	public function getImageFileCollection() {
		$imageFileCollection = new ImageFileCollection('transport');
		$imageFileCollection->addImageFile($this->getId() . '.' . $this->image, $this->getName());
		return $imageFileCollection;
	}

	/**
	 * @param string|null $cachedFilename
	 */
	public function setImageForUpload($cachedFilename) {
		$this->imageForUpload = $cachedFilename;
	}

	/**
	 * @return integer 
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
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @return string|null 
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return boolean
	 */
	public function isHidden() {
		return $this->hidden;
	}
	
	/**
	 * @return boolean
	 */
	public function isDeleted() {
		return $this->deleted;
	}

	/**
	 * @param boolean $deleted
	 */
	public function markAsDeleted() {
		$this->deleted = true;
	}
}
