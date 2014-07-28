<?php

namespace SS6\ShopBundle\Model\Product;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Image\ImageFileCollection;
use SS6\ShopBundle\Model\FileUpload\EntityFileUploadInterface;
use SS6\ShopBundle\Model\FileUpload\FileForUpload;
use SS6\ShopBundle\Model\FileUpload\FileNamingConvention;

/**
 * Product
 *
 * @ORM\Table(name="products")
 * @ORM\Entity
 */
class Product implements EntityFileUploadInterface {

	/**
	 * @var integer
	 *
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
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $catnum;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $partno;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $ean;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $description;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6, nullable=true)
	 */
	private $price;
	
	/**
	 * @var \DateTime|null
	 * 
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $sellingFrom;
	
	/**
	 * @var \DateTime|null
	 * 
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $sellingTo;
	
	/**
	 * @var int|null
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $stockQuantity;
	
	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $hidden;
	
	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $visible;

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
	 * @param string|null $catnum
	 * @param string|null $partno
	 * @param string|null $ean
	 * @param string|null $description
	 * @param string|null $price
	 * @param \DateTime|null $sellingFrom
	 * @param \DateTime|null $sellingTo
	 * @param int|null $stockQuantity
	 * @param boolean $hidden
	 */
	public function __construct($name, $catnum = null, $partno = null, $ean = null,
			$description = null, $price = null, $sellingFrom = null, $sellingTo = null,
			$stockQuantity = null, $hidden = false) {
		$this->name = $name;
		$this->catnum = $catnum;
		$this->partno = $partno;
		$this->ean = $ean;
		$this->description = $description;
		$this->price = $price;
		$this->sellingFrom = $sellingFrom;
		$this->sellingTo = $sellingTo;
		$this->stockQuantity = $stockQuantity;
		$this->hidden = $hidden;
		$this->visible = false;
		$this->image = null;
	}
	
	/**
	 * @param string $name
	 * @param string|null $catnum
	 * @param string|null $partno
	 * @param string|null $ean
	 * @param string|null $description
	 * @param string|null $price
	 * @param \DateTime|null $sellingFrom
	 * @param \DateTime|null $sellingTo
	 * @param int|null $stockQuantity
	 * @param boolean $hidden
	 */
	public function edit($name, $catnum, $partno, $ean, $description, $price, $sellingFrom,
			$sellingTo, $stockQuantity, $hidden) {
		$this->name = $name;
		$this->catnum = $catnum;
		$this->partno = $partno;
		$this->ean = $ean;
		$this->description = $description;
		$this->price = $price;
		$this->sellingFrom = $sellingFrom;
		$this->sellingTo = $sellingTo;
		$this->stockQuantity = $stockQuantity;
		$this->hidden = $hidden;
	}

	/**
	 * @return \SS6\ShopBundle\Model\FileUpload\FileForUpload[]
	 */
	public function getCachedFilesForUpload() {
		$files = array();
		if ($this->imageForUpload !== null) {
			$files['image'] = new FileForUpload($this->imageForUpload, true, 'product', 'default', FileNamingConvention::TYPE_ID);
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
		$imageFileCollection = new ImageFileCollection('product');
		$imageFileCollection->addImageFile($this->getId() . '.' . $this->image, $this->getName(), 'default');
		return $imageFileCollection;
	}

	/**
	 * @return string
	 */
	public function getImageFilename() {
		return $this->getId() . '.' . $this->image;
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
	 * @return string|null
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string|null
	 */
	public function getCatnum() {
		return $this->catnum;
	}

	/**
	 * @return string|null
	 */
	public function getPartno() {
		return $this->partno;
	}

	/**
	 * @return string|null
	 */
	public function getEan() {
		return $this->ean;
	}

	/**
	 * @return string|null
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return string|null
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @return DateTime|null
	 */
	public function getSellingFrom() {
		return $this->sellingFrom;
	}

	/**
	 * @return DateTime|null
	 */
	public function getSellingTo() {
		return $this->sellingTo;
	}
	
	/**
	 * @return int|null
	 */
	public function getStockQuantity() {
		return $this->stockQuantity;
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
	public function isVisible() {
		return $this->visible;
	}

}
