<?php

namespace Shopsys\ShopBundle\Tests\Database\Model\Product;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\DataFixtures\Base\FlagDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\BrandDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Product\Filter\ParameterFilterData;
use Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData;
use Shopsys\ShopBundle\Model\Product\Listing\ProductListOrderingModeService;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterRepository;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterValue;
use Shopsys\ShopBundle\Model\Product\ProductOnCurrentDomainFacade;
use Shopsys\ShopBundle\Tests\Test\DatabaseTestCase;

class ProductOnCurrentDomainFacadeTest extends DatabaseTestCase {

	public function testFilterByMinimalPrice() {
		$category = $this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::TV);

		$productFilterData = new ProductFilterData();
		$productFilterData->minimalPrice = 1000;
		$paginationResult = $this->getPaginationResultInCategory($productFilterData, $category);

		$this->assertCount(22, $paginationResult->getResults());
	}

	public function testFilterByMaximalPrice() {
		$category = $this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::TV);

		$productFilterData = new ProductFilterData();
		$productFilterData->maximalPrice = 10000;
		$paginationResult = $this->getPaginationResultInCategory($productFilterData, $category);

		$this->assertCount(22, $paginationResult->getResults());
	}

	public function testFilterByStockAvailability() {
		$category = $this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::PHONES);

		$productFilterData = new ProductFilterData();
		$productFilterData->inStock = true;
		$paginationResult = $this->getPaginationResultInCategory($productFilterData, $category);

		$this->assertCount(2, $paginationResult->getResults());
	}

	public function testFilterByFlag() {
		$category = $this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::PRINTERS);

		$flagTopProduct = $this->getReference(FlagDataFixture::TOP_PRODUCT);
		$productFilterData = new ProductFilterData();
		$productFilterData->flags = [$flagTopProduct];
		$paginationResult = $this->getPaginationResultInCategory($productFilterData, $category);

		$this->assertCount(2, $paginationResult->getResults());
	}

	public function testFilterByFlagsReturnsProductsWithAnyOfUsedFlags() {
		$category = $this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::BOOKS);

		$flagTopProduct = $this->getReference(FlagDataFixture::TOP_PRODUCT);
		$flagActionProduct = $this->getReference(FlagDataFixture::ACTION_PRODUCT);
		$productFilterData = new ProductFilterData();
		$productFilterData->flags = [$flagTopProduct, $flagActionProduct];
		$paginationResult = $this->getPaginationResultInCategory($productFilterData, $category);

		$this->assertCount(5, $paginationResult->getResults());
	}

	public function testFilterByBrand() {
		$category = $this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::PRINTERS);

		$brandCanon = $this->getReference(BrandDataFixture::CANON);
		$productFilterData = new ProductFilterData();
		$productFilterData->brands = [$brandCanon];
		$paginationResult = $this->getPaginationResultInCategory($productFilterData, $category);

		$this->assertCount(6, $paginationResult->getResults());
	}

	public function testFilterByBrandsReturnsProductsWithAnyOfUsedBrands() {
		$category = $this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::PRINTERS);

		$brandHp = $this->getReference(BrandDataFixture::HP);
		$brandCanon = $this->getReference(BrandDataFixture::CANON);
		$productFilterData = new ProductFilterData();
		$productFilterData->brands = [$brandCanon, $brandHp];
		$paginationResult = $this->getPaginationResultInCategory($productFilterData, $category);

		$this->assertCount(8, $paginationResult->getResults());
	}

	public function testFilterByParameter() {
		$category = $this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::PRINTERS);

		$parameterFilterData = $this->createParameterFilterData(
			['cs' => 'Rozlišení tisku'],
			[['cs' => '4800x1200']]
		);
		$productFilterData = new ProductFilterData();
		$productFilterData->parameters = [$parameterFilterData];

		$paginationResult = $this->getPaginationResultInCategory($productFilterData, $category);

		$this->assertCount(3, $paginationResult->getResults());
	}

	public function testFilterByParametersUsesOrWithinTheSameParameter() {
		$category = $this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::PRINTERS);

		$parameterFilterData = $this->createParameterFilterData(
			['cs' => 'Rozlišení tisku'],
			[
				['cs' => '4800x1200'],
				['cs' => '2400x600'],
			]
		);
		$productFilterData = new ProductFilterData();
		$productFilterData->parameters = [$parameterFilterData];
		$paginationResult = $this->getPaginationResultInCategory($productFilterData, $category);

		$this->assertCount(10, $paginationResult->getResults());
	}

	public function testFilterByParametersUsesAndWithinDistinctParameters() {
		$category = $this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::PRINTERS);

		$parameterFilterData1 = $this->createParameterFilterData(
			['cs' => 'Rozlišení tisku'],
			[['cs' => '2400x600']]
		);
		$parameterFilterData2 = $this->createParameterFilterData(
			['cs' => 'LCD'],
			[['cs' => 'Ano']]
		);
		$productFilterData = new ProductFilterData();
		$productFilterData->parameters = [$parameterFilterData1, $parameterFilterData2];
		$paginationResult = $this->getPaginationResultInCategory($productFilterData, $category);

		$this->assertCount(2, $paginationResult->getResults());
	}

	/**
	 * @param array $namesByLocale
	 * @param array $valuesTextsByLocales
	 * @return \Shopsys\ShopBundle\Model\Product\Filter\ParameterFilterData
	 */
	private function createParameterFilterData(array $namesByLocale, array $valuesTextsByLocales) {
		$parameterRepository = $this->getContainer()->get(ParameterRepository::class);
		/* @var $parameterRepository \Shopsys\ShopBundle\Model\Product\Parameter\ParameterRepository */

		$parameter = $parameterRepository->findParameterByNames($namesByLocale);
		$parameterValues = $this->getParameterValuesByLocalesAndTexts($valuesTextsByLocales);

		$parameterFilterData = new ParameterFilterData();
		$parameterFilterData->parameter = $parameter;
		$parameterFilterData->values = $parameterValues;

		return $parameterFilterData;
	}

	/**
	 * @param array[] $valuesTextsByLocales
	 * @return \Shopsys\ShopBundle\Model\Product\Parameter\ParameterValue[]
	 */
	private function getParameterValuesByLocalesAndTexts(array $valuesTextsByLocales) {
		$em = $this->getContainer()->get(EntityManager::class);
		/* @var $em \Doctrine\ORM\EntityManager */
		$parameterValues = [];

		foreach ($valuesTextsByLocales as $valueTextsByLocales) {
			foreach ($valueTextsByLocales as $locale => $text) {
				$parameterValues[] = $em->getRepository(ParameterValue::class)->findBy([
					'text' => $text,
					'locale' => $locale,
				]);
			}
		}

		return $parameterValues;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @param \Shopsys\ShopBundle\Model\Category\Category $category
	 * @return \Shopsys\ShopBundle\Component\Paginator\PaginationResult
	 */
	private function getPaginationResultInCategory(ProductFilterData $productFilterData, Category $category) {
		$productOnCurrentDomainFacade = $this->getContainer()->get(ProductOnCurrentDomainFacade::class);
		/* @var $productOnCurrentDomainFacade \Shopsys\ShopBundle\Model\Product\ProductOnCurrentDomainFacade */
		$page = 1;
		$limit = PHP_INT_MAX;

		return $productOnCurrentDomainFacade->getPaginatedProductDetailsInCategory(
			$productFilterData,
			ProductListOrderingModeService::ORDER_BY_NAME_ASC,
			$page,
			$limit,
			$category->getId()
		);
	}
}
