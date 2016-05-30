<?php

namespace SS6\ShopBundle\Tests\Database\Model\Unit;

use SS6\ShopBundle\DataFixtures\Base\UnitDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Product\ProductEditDataFactory;
use SS6\ShopBundle\Model\Product\ProductEditFacade;
use SS6\ShopBundle\Model\Product\Unit\UnitData;
use SS6\ShopBundle\Model\Product\Unit\UnitFacade;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

/**
 * @UglyTest
 */
class UnitFacadeTest extends DatabaseTestCase {

	public function testDeleteByIdAndReplace() {
		$em = $this->getEntityManager();
		$unitFacade = $this->getContainer()->get(UnitFacade::class);
		/* @var $unitFacade \SS6\ShopBundle\Model\Product\Unit\UnitFacade */
		$productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
		/* @var $productEditDataFactory \SS6\ShopBundle\Model\Product\ProductEditDataFactory */
		$productEditFacade = $this->getContainer()->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */

		$unitToDelete = $unitFacade->create(new UnitData(['cs' => 'name']));
		$unitToReplaceWith = $this->getReference(UnitDataFixture::PCS);
		/* @var $newUnit \SS6\ShopBundle\Model\Product\Unit\Unit */
		$product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
		/* @var $product \SS6\ShopBundle\Model\Product\Product */
		$productEditData = $productEditDataFactory->createFromProduct($product);
		/* @var $productEditData \SS6\ShopBundle\Model\Product\ProductEditData */

		$productEditData->productData->unit = $unitToDelete;
		$productEditFacade->edit($product->getId(), $productEditData);

		$unitFacade->deleteById($unitToDelete->getId(), $unitToReplaceWith->getId());

		$em->refresh($product);

		$this->assertEquals($unitToReplaceWith, $product->getUnit());
	}

}
