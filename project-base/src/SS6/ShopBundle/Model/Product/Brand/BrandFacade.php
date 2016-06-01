<?php

namespace SS6\ShopBundle\Model\Product\Brand;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Image\ImageFacade;
use SS6\ShopBundle\Model\Product\Brand\Brand;
use SS6\ShopBundle\Model\Product\Brand\BrandData;
use SS6\ShopBundle\Model\Product\Brand\BrandRepository;

class BrandFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Brand\BrandRepository
	 */
	private $brandRepository;

	/**
	 * @var \SS6\ShopBundle\Component\Image\ImageFacade
	 */
	private $imageFacade;

	public function __construct(
		EntityManager $em,
		BrandRepository $brandRepository,
		ImageFacade $imageFacade
	) {
		$this->em = $em;
		$this->brandRepository = $brandRepository;
		$this->imageFacade = $imageFacade;
	}

	/**
	 * @param int $brandId
	 * @return \SS6\ShopBundle\Model\Product\Brand\Brand
	 */
	public function getById($brandId) {
		return $this->brandRepository->getById($brandId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Brand\BrandData $brandData
	 * @return \SS6\ShopBundle\Model\Product\Brand\Brand
	 */
	public function create(BrandData $brandData) {
		$brand = new Brand($brandData);
		$this->em->persist($brand);
		$this->em->flush();
		$this->imageFacade->uploadImage($brand, $brandData->image, null);

		return $brand;
	}

	/**
	 * @param int $brandId
	 * @param \SS6\ShopBundle\Model\Product\Brand\BrandData $brandData
	 * @return \SS6\ShopBundle\Model\Product\Brand\Brand
	 */
	public function edit($brandId, BrandData $brandData) {
		$brand = $this->brandRepository->getById($brandId);
		$brand->edit($brandData);
		$this->imageFacade->uploadImage($brand, $brandData->image, null);
		$this->em->flush();

		return $brand;
	}

	/**
	 * @param int $brandId
	 */
	public function deleteById($brandId) {
		$brand = $this->brandRepository->getById($brandId);
		$this->em->remove($brand);
		$this->em->flush();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Brand\Brand[]
	 */
	public function getAll() {
		return $this->brandRepository->getAll();
	}

}
