<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use DateTime;
use SS6\ShopBundle\Model\Product\Parameter\ParameterData;
use SS6\ShopBundle\Model\Product\Parameter\ParameterFacade;
use SS6\ShopBundle\Model\Product\Parameter\ParameterValueData;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData;
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
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterFacade
	 */
	private $parameterFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat[]
	 */
	private $vats;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\Availability[]
	 */
	private $availabilities;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\Parameter[]
	 */
	private $parameters;

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
		ParameterFacade $parameterFacade,
		ProductEditDataFactory $productEditDataFactory
	) {
		$this->parameterFacade = $parameterFacade;
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
	public function injectReferences(
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
		$this->parameters = [];
		$this->units = $units;
		$this->pricingGroups = $pricingGroups;
	}

	/**
	 * @param array $row
	 * @return \SS6\ShopBundle\Model\Product\ProductEditData
	 */
	public function getProductEditDataFromRow($row) {
		$productEditData = $this->productEditDataFactory->createDefault();
		$this->updateProductEditDataFromCsvRowForFirstDomain($productEditData, $row);
		$this->updateProductEditDataFromCsvRowForSecondDomain($productEditData, $row);

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
		$productEditData->parameters = $this->getProductParameterValuesDataFromString(
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
	 * @param \SS6\ShopBundle\Model\Product\ProductEditData $productEditData
	 * @param array $row
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	private function updateProductEditDataFromCsvRowForSecondDomain(ProductEditData $productEditData, array $row) {
		$domainId = 2;
		$locale = 'en';
		$productEditData->productData->name[$locale] = $row[self::COLUMN_NAME_EN];
		$productEditData->descriptions[$domainId] = $row[self::COLUMN_DESCRIPTION_EN];
		$productEditData->shortDescriptions[$domainId] = $row[self::COLUMN_SHORT_DESCRIPTION_EN];
		$productEditData->showInZboziFeed[$domainId] = true;
		$this->setProductDataPricesFromCsv($row, $productEditData, $domainId);
		$productEditData->parameters = $this->getProductParameterValuesDataFromString(
			$row[self::COLUMN_PARAMETERS],
			$domainId
		);
		$productEditData->productData->categoriesByDomainId[$domainId] =
			$this->getValuesByKeyString($row[self::COLUMN_CATEGORIES_2], $this->categories);
	}

	/**
	 * @param string $string
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[]
	 */
	private function getProductParameterValuesDataFromString($string, $domainId) {
		$rows = explode(';', $string);

		$productParameterValuesData = [];
		foreach ($rows as $row) {
			$rowData = explode('=', $row);
			if (count($rowData) !== 2) {
				continue;
			}
			list($serializedParameterNames, $serializedValueTexts) = $rowData;
			$serializedParameterNames = trim($serializedParameterNames, '[]');
			$serializedValueTexts = trim($serializedValueTexts, '[]');

			$productParameterValuesData = $this->addProductParameterValuesData(
				$productParameterValuesData,
				$domainId,
				$serializedParameterNames,
				$serializedValueTexts
			);
		}

		return $productParameterValuesData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[] $productParameterValuesData
	 * @param int $domainId
	 * @param string $serializedParameterNames
	 * @param string $serializedValueTexts
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[]
	 */
	private function addProductParameterValuesData(
		array $productParameterValuesData,
		$domainId,
		$serializedParameterNames,
		$serializedValueTexts
	) {
		$csvParameterNames = $this->unserializeLocalizedValues($serializedParameterNames);

		if (!isset($this->parameters[$serializedParameterNames])) {
			$csPrameterNames = [
				'cs' => $csvParameterNames['cs'],
			];
			if ($domainId === 1) {
				$parametersNames = $csPrameterNames;
			} else {
				$parametersNames = $csvParameterNames;
			}
			$parameter = $this->parameterFacade->findParameterByNames($csPrameterNames);
			if ($parameter === null) {
				$parameter = $this->parameterFacade->create(new ParameterData($parametersNames, true));
			}
			$this->parameters[$serializedParameterNames] = $parameter;
		} else {
			$parameter = $this->parameters[$serializedParameterNames];
		}

		if (
			$domainId === 2
			&& array_key_exists('en', $csvParameterNames)
			&& $parameter->getName('en') !== $csvParameterNames['en']
		) {
			$parameterData = new ParameterData();
			$parameterData->setFromEntity($parameter);
			$parameterData->name['en'] = $csvParameterNames['en'];
			$this->parameterFacade->edit($parameter->getId(), $parameterData);
		}

		$valueTexts = $this->unserializeLocalizedValues($serializedValueTexts);
		foreach ($valueTexts as $locale => $valueText) {
			if ($domainId === 1 && $locale === 'en') {
				continue;
			}
			$productParameterValueData = new ProductParameterValueData();
			$productParameterValueData->parameterValueData = new ParameterValueData($valueText, $locale);
			$productParameterValueData->parameter = $parameter;
			$productParameterValuesData[] = $productParameterValueData;
		}

		return $productParameterValuesData;
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
	 * @param string $string
	 * @return string[locale]
	 */
	private function unserializeLocalizedValues($string) {
		$array = [];
		$items = explode(',', $string);
		foreach ($items as $item) {
			list($locale, $value) = explode(':', $item);
			$array[$locale] = $value;
		}
		return $array;
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

}
