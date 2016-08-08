<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Product\Product;

/**
 * @ORM\Table(name="product_domains")
 * @ORM\Entity
 */
class ProductDomain {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Product")
	 * @ORM\JoinColumn(nullable=false, name="product_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $product;

	/**
	 * @var int
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 */
	private $domainId;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $seoTitle;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $seoMetaDescription;

	/**
	 * @var string|null
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $description;

	/**
	 * @var string|null
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $shortDescription;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="tsvector", nullable=false)
	 */
	private $descriptionTsvector;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="tsvector", nullable=false)
	 */
	private $fulltextTsvector;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="decimal", precision=16, scale=2, nullable=true)
	 */
	private $heurekaCpc;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean", nullable=false)
	 */
	private $showInZboziFeed;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="decimal", precision=16, scale=2, nullable=true)
	 */
	private $zboziCpc;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="decimal", precision=16, scale=2, nullable=true)
	 */
	private $zboziCpcSearch;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @param bool $showInZboziFeed
	 */
	public function __construct(Product $product, $domainId, $showInZboziFeed = true) {
		$this->product = $product;
		$this->domainId = $domainId;
		$this->showInZboziFeed = $showInZboziFeed;
	}

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

	/**
	 * @return string|null
	 */
	public function getSeoTitle() {
		return $this->seoTitle;
	}

	/**
	 * @return string|null
	 */
	public function getSeoMetaDescription() {
		return $this->seoMetaDescription;
	}

	/**
	 * @param string|null $seoTitle
	 */
	public function setSeoTitle($seoTitle) {
		$this->seoTitle = $seoTitle;
	}

	/**
	 * @param string|null $seoMetaDescription
	 */
	public function setSeoMetaDescription($seoMetaDescription) {
		$this->seoMetaDescription = $seoMetaDescription;
	}

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Domain $domain
	 * @return string
	 */
	public function getSeoTitleForHtml(Domain $domain) {
		$seoTitle = $this->getSeoTitle();
		if ($seoTitle === null) {
			return $this->product->getName($domain->getLocale());
		} else {
			return $seoTitle;
		}
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
	public function getShortDescription() {
		return $this->shortDescription;
	}

	/**
	 * @param string|null $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @param string|null $shortDescription
	 */
	public function setShortDescription($shortDescription) {
		$this->shortDescription = $shortDescription;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @return string|null
	 */
	public function getHeurekaCpc() {
		return $this->heurekaCpc;
	}

	/**
	 * @param string|null $heurekaCpc
	 */
	public function setHeurekaCpc($heurekaCpc) {
		$this->heurekaCpc = $heurekaCpc;
	}

	/**
	 * @return bool
	 */
	public function getShowInZboziFeed() {
		return $this->showInZboziFeed;
	}

	/**
	 * @param bool $showInZboziFeed
	 */
	public function setShowInZboziFeed($showInZboziFeed) {
		$this->showInZboziFeed = $showInZboziFeed;
	}

	/**
	 * @return string|null
	 */
	public function getZboziCpc() {
		return $this->zboziCpc;
	}

	/**
	 * @param string|null $zboziCpc
	 */
	public function setZboziCpc($zboziCpc) {
		$this->zboziCpc = $zboziCpc;
	}

	/**
	 * @return string|null
	 */
	public function getZboziCpcSearch() {
		return $this->zboziCpcSearch;
	}

	/**
	 * @param string|null $zboziCpcSearch
	 */
	public function setZboziCpcSearch($zboziCpcSearch) {
		$this->zboziCpcSearch = $zboziCpcSearch;
	}

}
