<?php

namespace SS6\ShopBundle\Model\Product\Brand;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Product\Brand\BrandData;

/**
 * @ORM\Table(name="brands")
 * @ORM\Entity
 */
class Brand {

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255, nullable=false)
	 */
	private $name;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Brand\BrandData $brandData
	 */
	public function __construct(BrandData $brandData) {
		$this->name = $brandData->name;
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
	 * @param \SS6\ShopBundle\Model\Product\Brand\BrandData $brandData
	 */
	public function edit(BrandData $brandData) {
		$this->name = $brandData->name;
	}

}
