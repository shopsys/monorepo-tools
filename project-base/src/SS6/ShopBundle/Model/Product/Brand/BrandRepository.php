<?php

namespace SS6\ShopBundle\Model\Product\Brand;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Brand\Brand;

class BrandRepository {

	/**
	 * @var \Doctrine\ORM\EntityRepository
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		$this->em = $entityManager;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getBrandRepository() {
		return $this->em->getRepository(Brand::class);
	}

	/**
	 * @param int $brandId
	 * @return \SS6\ShopBundle\Model\Product\Brand\Brand
	 */
	public function getById($brandId) {
		$brand = $this->getBrandRepository()->find($brandId);

		if ($brand === null) {
			$message = 'Brand with ID ' . $brandId . ' not found.';
			throw new \SS6\ShopBundle\Model\Product\Brand\Exception\BrandNotFoundException($message);
		}

		return $brand;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Brand\Brand[]
	 */
	public function getAll() {
		return $this->getBrandRepository()->findBy([], ['name' => 'asc']);
	}

}
