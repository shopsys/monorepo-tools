<?php

namespace Shopsys\ShopBundle\Model\Product;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * @ORM\Table(name="product_translations")
 * @ORM\Entity
 */
class ProductTranslation extends AbstractTranslation {

	/**
	 * @Prezent\Translatable(targetEntity="Shopsys\ShopBundle\Model\Product\Product")
	 */
	protected $translatable;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="tsvector", nullable=false)
	 */
	private $nameTsvector;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $variantAlias;

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return string|null
	 */
	public function getVariantAlias() {
		return $this->variantAlias;
	}

	/**
	 * @param string|null $variantAlias
	 */
	public function setVariantAlias($variantAlias) {
		$this->variantAlias = $variantAlias;
	}
}
