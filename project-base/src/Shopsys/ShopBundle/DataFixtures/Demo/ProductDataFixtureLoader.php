<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use DateTime;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductEditData;
use SS6\ShopBundle\Model\Product\ProductEditDataFactory;

class ProductDataFixtureLoader {

	const COLUMN_NAME_CS = 0;
	const COLUMN_NAME_EN = 1;
	const COLUMN_CATNUM = 2;
	const COLUMN_PARTNO = 3;
	const COLUMN_EAN = 4;
	const COLUMN_DESCRIPTION_CS = 5;
	const COLUMN_DESCRIPTION_EN = 6;
	const COLUMN_SHORT_DESCRIPTION_CS = 7;
	const COLUMN_SHORT_DESCRIPTION_EN = 8;
	const COLUMN_PRICE_CALCULATION_TYPE = 9;
	const COLUMN_MAIN_PRICE = 10;
	const COLUMN_MANUAL_PRICES_DOMAIN_1 = 11;
	const COLUMN_MANUAL_PRICES_DOMAIN_2 = 12;
	const COLUMN_VAT = 13;
	const COLUMN_SELLING_FROM = 14;
	const COLUMN_SELLING_TO = 15;
	const COLUMN_STOCK_QUANTITY = 16;
	const COLUMN_UNIT = 17;
	const COLUMN_AVAILABILITY = 18;
	const COLUMN_PARAMETERS = 19;
	const COLUMN_CATEGORIES_1 = 20;
	const COLUMN_CATEGORIES_2 = 21;
	const COLUMN_FLAGS = 22;
	const COLUMN_SELLING_DENIED = 23;
	const COLUMN_BRAND = 24;
	const COLUMN_MAIN_VARIANT_CATNUM = 25;

	/**
	 * @var \SS6\ShopBundle\DataFixtures\Demo\ProductParametersFixtureLoader
	 */
	private $productParametersFixtureLoader;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat[]
	 */
	private $vats;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\Availability[]
	 */
	private $availabilities;

	/**
	 * @var \SS6\ShopBundle\Model\Category\Category[]
	 */
	private $categories;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Flag\Flag[]
	 */
	private $flags;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Brand\Brand[]
	 */
	private $brands;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Unit\Unit[]
	 */
	private $units;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]
	 */
	private $pricingGroups;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductEditDataFactory
	 */
	private $productEditDataFactory;

	public function __construct(
		ProductParametersFixtureLoader $productParametersFixtureLoader,
		ProductEditDataFactory $productEditDataFactory
	) {
		$this->productParametersFixtureLoader = $productParametersFixtureLoader;
		$this->productEditDataFactory = $productEditDataFactory;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat[] $vats
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability[] $availabilities
	 * @param \SS6\ShopBundle\Model\Category\Category[] $categories
	 * @param \SS6\ShopBundle\Model\Product\Flag\Flag[] $flags
	 * @param \SS6\ShopBundle\Model\Product\Brand\Brand[] $brands
	 * @param \SS6\ShopBundle\Model\Product\Unit\Unit[] $units
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[] $pricingGroups
	 */
	public function refreshCachedEntities(
		array $vats,
		array $availabilities,
		array $categories,
		array $flags,
		array $brands,
		array $units,
		array $pricingGroups
	) {
		$this->vats = $vats;
		$this->availabilities = $availabilities;
		$this->categories = $categories;
		$this->flags = $flags;
		$this->brands = $brands;
		$this->units = $units;
		$this->pricingGroups = $pricingGroups;
		$this->productParametersFixtureLoader->clearCache();
	}

	/**
	 * @param array $row
	 * @return \SS6\ShopBundle\Model\Product\ProductEditData
	 */
	public function createProductEditDataFromRowForFirstDomain($row) {
		$productEditData = $this->productEditDataFactory->createDefault();
		$this->updateProductEditDataFromCsvRowForFirstDomain($productEditData, $row);

		return $productEditData;
	}

	/**
	 * @param array $rows
	 * @return string[mainVariantRowId][]
	 */
	public function getVariantCatnumsIndexedByMainVariantCatnum($rows) {
		$variantCatnumsByMainVariantCatnum = [];
		foreach ($rows as $row) {
			if ($row[self::COLUMN_MAIN_VARIANT_CATNUM] !== null && $row[self::COLUMN_CATNUM] !== null) {
				$variantCatnumsByMainVariantCatnum[$row[self::COLUMN_MAIN_VARIANT_CATNUM]][] = $row[self::COLUMN_CATNUM];
			}
		}

		return $variantCatnumsByMainVariantCatnum;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductEditData $productEditData
	 * @param array $row
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	private function updateProductEditDataFromCsvRowForFirstDomain(ProductEditData $productEditData, array $row) {
		$domainId = 1;
		$locale = 'cs';
		$productEditData->productData->name[$locale] = $row[self::COLUMN_NAME_CS];
		$productEditData->productData->catnum = $row[self::COLUMN_CATNUM];
		$productEditData->productData->partno = $row[self::COLUMN_PARTNO];
		$productEditData->productData->ean = $row[self::COLUMN_EAN];
		$productEditData->descriptions[$domainId] = $row[self::COLUMN_DESCRIPTION_CS];
		$productEditData->shortDescriptions[$domainId] = $row[self::COLUMN_SHORT_DESCRIPTION_CS];
		$productEditData->showInZboziFeed[$domainId] = true;
		$productEditData->productData->priceCalculationType = $row[self::COLUMN_PRICE_CALCULATION_TYPE];
		$this->setProductDataPricesFromCsv($row, $productEditData, $domainId);
		switch ($row[self::COLUMN_VAT]) {
			case 'high':
				$productEditData->productData->vat = $this->vats['high'];
				break;
			case 'low':
				$productEditData->productData->vat = $this->vats['low'];
				break;
			case 'second_low':
				$productEditData->productData->vat = $this->vats['second_low'];
				break;
			case 'zero':
				$productEditData->productData->vat = $this->vats['zero'];
				break;
			default:
				$productEditData->productData->vat = null;
		}
		if ($row[self::COLUMN_SELLING_FROM] !== null) {
			$productEditData->productData->sellingFrom = new DateTime($row[self::COLUMN_SELLING_FROM]);
		}
		if ($row[self::COLUMN_SELLING_TO] !== null) {
			$productEditData->productData->sellingTo = new DateTime($row[self::COLUMN_SELLING_TO]);
		}
		$productEditData->productData->usingStock = $row[self::COLUMN_STOCK_QUANTITY] !== null;
		$productEditData->productData->stockQuantity = $row[self::COLUMN_STOCK_QUANTITY];
		$productEditData->productData->unit = $this->units[$row[self::COLUMN_UNIT]];
		$productEditData->productData->outOfStockAction = Product::OUT_OF_STOCK_ACTION_HIDE;
		switch ($row[self::COLUMN_AVAILABILITY]) {
			case 'in-stock':
				$productEditData->productData->availability = $this->availabilities['in-stock'];
				break;
			case 'out-of-stock':
				$productEditData->productData->availability = $this->availabilities['out-of-stock'];
				break;
			case 'on-request':
				$productEditData->productData->availability = $this->availabilities['on-request'];
				break;
		}
		$productEditData->parameters = $this->productParametersFixtureLoader->getProductParameterValuesDataFromString(
			$row[self::COLUMN_PARAMETERS],
			$domainId
		);
		$productEditData->productData->categoriesByDomainId[$domainId] =
			$this->getValuesByKeyString($row[self::COLUMN_CATEGORIES_1], $this->categories);
		$productEditData->productData->flags = $this->getValuesByKeyString($row[self::COLUMN_FLAGS], $this->flags);
		$productEditData->productData->sellingDenied = $row[self::COLUMN_SELLING_DENIED];

		if ($row[self::COLUMN_BRAND] !== null) {
			$productEditData->productData->brand = $this->brands[$row[self::COLUMN_BRAND]];
		}
	}

	/**
	 * @param array $row
	 * @return string
	 */
	public function getCatnumFromRow($row) {
		return $row[self::COLUMN_CATNUM];
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductEditData $productEditData
	 * @param array $row
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function updateProductEditDataFromCsvRowForSecondDomain(ProductEditData $productEditData, array $row) {
		$domainId = 2;
		$locale = 'en';
		$productEditData->productData->name[$locale] = $row[self::COLUMN_NAME_EN];
		$productEditData->descriptions[$domainId] = $row[self::COLUMN_DESCRIPTION_EN];
		$productEditData->shortDescriptions[$domainId] = $row[self::COLUMN_SHORT_DESCRIPTION_EN];
		$productEditData->showInZboziFeed[$domainId] = true;
		$this->setProductDataPricesFromCsv($row, $productEditData, $domainId);
		$productEditData->parameters = $this->productParametersFixtureLoader->getProductParameterValuesDataFromString(
			$row[self::COLUMN_PARAMETERS],
			$domainId
		);
		$productEditData->productData->categoriesByDomainId[$domainId] =
			$this->getValuesByKeyString($row[self::COLUMN_CATEGORIES_2], $this->categories);
	}

	/**
	 * @param string $string
	 * @return string[pricingGroup]
	 */
	private function getProductManualPricesIndexedByPricingGroupFromString($string) {
		$productManualPrices = [];
		$rowData = explode(';', $string);
		foreach ($rowData as $pricingGroupAndPrice) {
			list($pricingGroup, $price) = explode('=', $pricingGroupAndPrice);
			$productManualPrices[$pricingGroup] = $price;
		}

		return $productManualPrices;
	}

	/**
	 * @param string $keyString
	 * @param array $valuesByKey
	 * @return string[]
	 */
	private function getValuesByKeyString($keyString, array $valuesByKey) {
		$values = [];
		if (!empty($keyString)) {
			$keys = explode(';', $keyString);
			foreach ($keys as $key) {
				$values[] = $valuesByKey[$key];
			}
		}

		return $values;
	}

	/**
	 * @param array $row
	 * @param \SS6\ShopBundle\Model\Product\ProductEditData $productEditData
	 * @param int $domainId
	 */
	private function setProductDataPricesFromCsv(array $row, ProductEditData $productEditData, $domainId) {
		switch ($row[self::COLUMN_PRICE_CALCULATION_TYPE]) {
			case 'auto':
				$productEditData->productData->price = $row[self::COLUMN_MAIN_PRICE];
				break;
			case 'manual':
				if ($domainId === 1) {
					$manualPricesColumn = $row[self::COLUMN_MANUAL_PRICES_DOMAIN_1];
				} elseif ($domainId === 2) {
					$manualPricesColumn = $row[self::COLUMN_MANUAL_PRICES_DOMAIN_2];
				}
				$manualPrices = $this->getProductManualPricesIndexedByPricingGroupFromString($manualPricesColumn);
				$this->createDefaultManualPriceForAllPricingGroups($productEditData);
				foreach ($manualPrices as $pricingGroup => $manualPrice) {
					$pricingGroup = $this->pricingGroups[$pricingGroup];
					$productEditData->manualInputPrices[$pricingGroup->getId()] = $manualPrice;
				}
				break;
			default:
				throw new \SS6\ShopBundle\Model\Product\Exception\InvalidPriceCalculationTypeException(
					$row[self::COLUMN_PRICE_CALCULATION_TYPE]
				);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductEditData $productEditData
	 */
	private function createDefaultManualPriceForAllPricingGroups(ProductEditData $productEditData) {
		foreach ($this->pricingGroups as $pricingGroupReferenceName => $pricingGroup) {
			if (!array_key_exists($pricingGroup->getId(), $productEditData->manualInputPrices)) {
				$productEditData->manualInputPrices[$pricingGroup->getId()] = null;
			}
		}
	}

}
