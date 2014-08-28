<?php

namespace SS6\ShopBundle\Model\Product;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
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
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Pricing\Vat\Vat")
	 */
	private $vat;
	
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
	 * @var \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Availability\Availability")
	 * @ORM\JoinColumn(name="availability_id", referencedColumnName="id", nullable=true)
	 */
	private $availability;
	
	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData
	 */
	public function __construct(ProductData $productData) {
		$this->name = $productData->getName();
		$this->catnum = $productData->getCatnum();
		$this->partno = $productData->getPartno();
		$this->ean = $productData->getEan();
		$this->description = $productData->getDescription();
		$this->price = $productData->getPrice();
		$this->vat = $productData->getVat();
		$this->sellingFrom = $productData->getSellingFrom();
		$this->sellingTo = $productData->getSellingTo();
		$this->stockQuantity = $productData->getStockQuantity();
		$this->hidden = $productData->getHidden();
		$this->visible = false;
		$this->image = null;
		$this->setImageForUpload($productData->getImage());
		$this->availability = $productData->getAvailability();
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData
	 */
	public function edit(ProductData $productData) {
		$this->name = $productData->getName();
		$this->catnum = $productData->getCatnum();
		$this->partno = $productData->getPartno();
		$this->ean = $productData->getEan();
		$this->description = $productData->getDescription();
		$this->price = $productData->getPrice();
		$this->vat = $productData->getVat();
		$this->sellingFrom = $productData->getSellingFrom();
		$this->sellingTo = $productData->getSellingTo();
		$this->stockQuantity = $productData->getStockQuantity();
		$this->hidden = $productData->getHidden();
		$this->setImageForUpload($productData->getImage());
		$this->availability = $productData->getAvailability();
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
	 * @param string $price
	 */
	public function setPrice($price) {
		$this->price = $price;
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
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public function getVat() {
		return $this->vat;
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

	/**
	 *
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 */
	public function getAvailability() {
		return $this->availability;
	}

}
