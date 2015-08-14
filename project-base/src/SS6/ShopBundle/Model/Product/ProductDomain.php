<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Domain\Domain;
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
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $hidden;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $seoTitle;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $seoMetaDescription;

	/**
	 * @var string
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $description;

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
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 */
	public function __construct(Product $product, $domainId) {
		$this->product = $product;
		$this->domainId = $domainId;
		$this->hidden = false;
	}

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

	/**
	 * @return bool
	 */
	public function isHidden() {
		return $this->hidden;
	}

	/**
	 * @param bool $hidden
	 */
	public function setHidden($hidden) {
		$this->hidden = $hidden;
	}

	/**
	 * @return string
	 */
	public function getSeoTitle() {
		return $this->seoTitle;
	}

	/**
	 * @return string
	 */
	public function getSeoMetaDescription() {
		return $this->seoMetaDescription;
	}

	/**
	 * @param string $seoTitle
	 */
	public function setSeoTitle($seoTitle) {
		$this->seoTitle = $seoTitle;
	}

	/**
	 * @param string $seoMetaDescription
	 */
	public function setSeoMetaDescription($seoMetaDescription) {
		$this->seoMetaDescription = $seoMetaDescription;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Domain $domain
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
	 * @param string|null $description
	 */
	public function setDescription($description) {
		$this->description = $description;
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

}
