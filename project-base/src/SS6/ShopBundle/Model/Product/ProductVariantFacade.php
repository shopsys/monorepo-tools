<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Image\ImageFacade;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductEditDataFactory;
use SS6\ShopBundle\Model\Product\ProductEditFacade;

class ProductVariantFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductEditFacade
	 */
	private $productEditFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductEditDataFactory
	 */
	private $productEditDataFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageFacade
	 */
	private $imageFacade;

	public function __construct(
		EntityManager $em,
		ProductEditFacade $productEditFacade,
		ProductEditDataFactory $productEditDataFactory,
		ImageFacade $imageFacade
	) {
		$this->em = $em;
		$this->productEditFacade = $productEditFacade;
		$this->productEditDataFactory = $productEditDataFactory;
		$this->imageFacade = $imageFacade;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $mainProduct
	 * @param \SS6\ShopBundle\Model\Product\Product[] $variants
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function createVariant(Product $mainProduct, array $variants) {
		$variants[] = $mainProduct;
		$mainProductEditData = $this->productEditDataFactory->createFromProduct($mainProduct);
		$newMainVariant = $this->productEditFacade->create($mainProductEditData);
		$this->imageFacade->copyImages($mainProduct, $newMainVariant);
		$newMainVariant->setVariants($variants);
		$this->em->flush();

		return $newMainVariant;
	}

}
