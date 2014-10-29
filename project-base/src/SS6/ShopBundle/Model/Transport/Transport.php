<?php

namespace SS6\ShopBundle\Model\Transport;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use SS6\ShopBundle\Model\Localize\AbstractTranslatableEntity;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Transport\TransportData;
use SS6\ShopBundle\Model\FileUpload\EntityFileUploadInterface;
use SS6\ShopBundle\Model\FileUpload\FileForUpload;
use SS6\ShopBundle\Model\FileUpload\FileNamingConvention;

/**
 * @ORM\Table(name="transports")
 * @ORM\Entity
 */
class Transport extends AbstractTranslatableEntity implements EntityFileUploadInterface {

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportTranslation[]
	 *
	 * @Prezent\Translations(targetEntity="SS6\ShopBundle\Model\Transport\TransportTranslation")
	 */
	protected $translations;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6)
	 */
	private $price;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Pricing\Vat\Vat")
	 */
	private $vat;

	/**
	 * @var bool
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
	 * @var int|null
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $position;

	private $currentTranslation;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\TransportData $transportData
	 */
	public function __construct(TransportData $transportData) {
		$this->translations = new ArrayCollection();

		$this->price = $transportData->getPrice();
		$this->vat = $transportData->getVat();
		$this->hidden = $transportData->isHidden();
		$this->deleted = false;
		$this->image = null;
		$this->setTranslations($transportData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\TransportData $transportData
	 */
	public function edit(TransportData $transportData) {
		$this->price = $transportData->getPrice();
		$this->vat = $transportData->getVat();
		$this->hidden = $transportData->isHidden();
		$this->setTranslations($transportData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\TransportData $transportData
	 */
	private function setTranslations(TransportData $transportData) {
		foreach ($transportData->getNames() as $locale => $name) {
			$this->translation($locale)->setName($name);
		}
		foreach ($transportData->getDescriptions() as $locale => $description) {
			$this->translation($locale)->setDescription($description);
		}
	}

	/**
	 * @param string|null $locale
	 * @return \SS6\ShopBundle\Model\Transport\TransportTranslation
	 */
	private function translation($locale = null) {
		if ($locale === null) {
			$locale = $this->currentLocale;
		}

		if (!$locale) {
			throw new \RuntimeException('No locale has been set and currentLocale is empty');
		}

		if ($this->currentTranslation && $this->currentTranslation->getLocale() === $locale) {
			return $this->currentTranslation;
		}

		$translation = $this->findTranslation($locale);
		if ($translation === null) {
			$translation = new TransportTranslation();
			$translation->setLocale($locale);
			$this->addTranslation($translation);
		}

		$this->currentTranslation = $translation;
		return $translation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 */
	public function changeVat(Vat $vat) {
		$this->vat = $vat;
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
	 * @return string|null
	 */
	public function getImageFilename() {
		if ($this->image !== null) {
			return $this->getId() . '.' . $this->image;
		}

		return null;
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
	 * @param string|null $locale
	 * @return string
	 */
	public function getName($locale = null) {
		return $this->translation($locale)->getName();
	}

	/**
	 * @return string
	 */
	public function getNames() {
		$names = array();
		foreach ($this->translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
		}

		return $names;
	}

	/**
	 * @param string|null $locale
	 * @return string|null
	 */
	public function getDescription($locale = null) {
		return $this->translation($locale)->getDescription();
	}

	/**
	 * @return string
	 */
	public function getDescriptions() {
		$descriptions = array();
		foreach ($this->translations as $translate) {
			$descriptions[$translate->getLocale()] = $translate->getDescription();
		}

		return $descriptions;
	}

	/**
	 * @return string
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
