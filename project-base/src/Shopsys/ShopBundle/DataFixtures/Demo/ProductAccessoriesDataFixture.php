<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\ShopBundle\Model\Product\ProductEditDataFactory;
use Shopsys\ShopBundle\Model\Product\ProductEditFacade;

class ProductAccessoriesDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$productEditDataFactory = $this->get(ProductEditDataFactory::class);
		/* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */
		$productEditFacade = $this->get(ProductEditFacade::class);
		/* @var $productEditFacade \Shopsys\ShopBundle\Model\Product\ProductEditFacade */
		$product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
		/* @var $product \Shopsys\ShopBundle\Model\Product\Product */

		$productEditData = $productEditDataFactory->createFromProduct($product);
		$productEditData->accessories = [
			$this->getReference(ProductDataFixture::PRODUCT_PREFIX . '24'),
			$this->getReference(ProductDataFixture::PRODUCT_PREFIX . '13'),
		];
		$productEditFacade->edit($product->getId(), $productEditData);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return [
			ProductDataFixture::class,
		];
	}

}
