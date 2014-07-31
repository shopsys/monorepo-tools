<?php

namespace SS6\ShopBundle\Model\Payment;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\FileUpload\EntityFileUploadInterface;
use SS6\ShopBundle\Model\FileUpload\FileForUpload;
use SS6\ShopBundle\Model\FileUpload\FileNamingConvention;
use SS6\ShopBundle\Model\Transport\Transport;

/**
 * @ORM\Table(name="payments")
 * @ORM\Entity
 */
class Payment implements EntityFileUploadInterface {

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
	 * @var Collection
	 * 
	 * @ORM\ManyToMany(targetEntity="SS6\ShopBundle\Model\Transport\Transport")
	 * @ORM\JoinTable(name="payments_transports")
	 */
	private $transports;

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
		$this->transports = new ArrayCollection();
		$this->hidden = $hidden;
		$this->deleted = false;
		$this->image = null;
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 */
	public function addTransport(Transport $transport) {
		if (!$this->transports->contains($transport)) {
			$this->transports->add($transport);
		}
	}
	
	/**
	 * @param array $transports
	 */
	public function setTransports(array $transports) {
		$this->transports->clear();
		foreach ($transports as $transport) {
			/* @var $transport \SS6\ShopBundle\Model\Transport\Transport */
			$this->addTransport($transport);
		}
	}

	/**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getTransports() {
		return $this->transports;
	}
	
	/**
	 * @param string $name
	 * @param string $price
	 * @param string|boolean $description
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
			$files['image'] = new FileForUpload($this->imageForUpload, true, 'payment', null, FileNamingConvention::TYPE_ID);
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
		$this->transports->clear();
	}
	
	/**
	 * @param boolean $withoutRelations
	 * @return boolean
	 */
	public function isVisible() {
		if ($this->isHidden() || $this->getTransports()->isEmpty()) {
			return false;
		}
		
		foreach ($this->getTransports() as $transport) {
			/* @var $transport \SS6\ShopBundle\Model\Transport\Transport */
			if (!$transport->isHidden()) {
				return true;
			}
		}
		return false;
	}
}
