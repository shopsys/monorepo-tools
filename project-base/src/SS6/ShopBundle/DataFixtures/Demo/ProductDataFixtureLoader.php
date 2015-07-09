<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use DateTime;
use SS6\ShopBundle\Component\Csv\CsvDecoder;
use SS6\ShopBundle\Component\Csv\CsvReader;
use SS6\ShopBundle\Component\String\EncodingConverter;
use SS6\ShopBundle\Component\String\TransformString;
use SS6\ShopBundle\Model\Product\Parameter\ParameterData;
use SS6\ShopBundle\Model\Product\Parameter\ParameterFacade;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Product\ProductEditData;

class ProductDataFixtureLoader {

	/**
	 * @var CsvReader
	 */
	private $csvReader;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterFacade
	 */
	private $parameterFacade;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var array
	 */
	private $vats;

	/**
	 * @var array
	 */
	private $availabilities;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\Parameter[]
	 */
	private $parameters;

	/**
	 * @var array
	 */
	private $categories;

	/**
	 * @var array
	 */
	private $flags;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Brand\Brand[]
	 */
	private $brands;

	/**
	 * @param string $path
	 * @param \SS6\ShopBundle\Component\Csv\CsvReader $csvReader
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterFacade $parameterFacade
	 */
	public function __construct($path, CsvReader $csvReader, ParameterFacade $parameterFacade) {
		$this->path = $path;
		$this->csvReader = $csvReader;
		$this->parameterFacade = $parameterFacade;
	}

	/**
	 * @param array $vats
	 * @param array $availabilities
	 * @param array $categories
	 * @param array $flags
	 * @param \SS6\ShopBundle\Model\Product\Brand\Brand[] $brands
	 */
	public function injectReferences(array $vats, array $availabilities, array $categories, array $flags, array $brands) {
		$this->vats = $vats;
		$this->availabilities = $availabilities;
		$this->categories = $categories;
		$this->flags = $flags;
		$this->brands = $brands;
		$this->parameters = [];
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\ProductEditData[]
	 */
	public function getProductsEditData() {
		$rows = $this->csvReader->getRowsFromCsv($this->path);

		$rowId = 0;
		foreach ($rows as $row) {
			if ($rowId !== 0) {
				$row = array_map([TransformString::class, 'emptyToNull'], $row);
				$row = EncodingConverter::cp1250ToUtf8($row);
				$productsEditData[] = $this->getProductEditDataFromCsvRow($row);
			}
			$rowId++;
		}
		return $productsEditData;
	}

	/**
	 * @param array $row
	 * @return \SS6\ShopBundle\Model\Product\ProductEditData
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	private function getProductEditDataFromCsvRow(array $row) {
		$productEditData = new ProductEditData();
		$productEditData->productData = new ProductData();
		$productEditData->productData->name = ['cs' => $row[0], 'en' => $row[1]];
		$productEditData->productData->catnum = $row[2];
		$productEditData->productData->partno = $row[3];
		$productEditData->productData->ean = $row[4];
		$productEditData->descriptions = [1 => $row[5], 2 => $row[6]];
		$productEditData->productData->price = $row[7];
		switch ($row[8]) {
			case 'high':
				$productEditData->productData->vat = $this->vats['high'];
				break;
			case 'low':
				$productEditData->productData->vat = $this->vats['low'];
				break;
			case 'zero':
				$productEditData->productData->vat = $this->vats['zero'];
				break;
			default:
				$productEditData->productData->vat = null;
		}
		if ($row[9] !== null) {
			$productEditData->productData->sellingFrom = new DateTime($row[9]);
		}
		if ($row[10] !== null) {
			$productEditData->productData->sellingTo = new DateTime($row[10]);
		}
		$productEditData->productData->usingStock = $row[11] !== null;
		$productEditData->productData->stockQuantity = $row[11];
		$productEditData->productData->outOfStockAction = Product::OUT_OF_STOCK_ACTION_HIDE;
		$hiddenOnDomains = [];
		if (!CsvDecoder::decodeBoolean($row[12])) {
			$hiddenOnDomains[] = 1;
		}
		if (!CsvDecoder::decodeBoolean($row[13])) {
			$hiddenOnDomains[] = 2;
		}
		$productEditData->productData->hiddenOnDomains = $hiddenOnDomains;
		switch ($row[14]) {
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
		$productEditData->parameters = $this->getProductParameterValuesDataFromString($row[15]);
		$productEditData->productData->categories = $this->getProductDataFromString($row[16], $this->categories);
		$productEditData->productData->flags = $this->getProductDataFromString($row[17], $this->flags);
		$productEditData->productData->sellingDenied = $row[18];

		if ($row[19] !== null) {
			$productEditData->productData->brand = $this->brands[$row[19]];
		}

		return $productEditData;
	}

	/**
	 * @param string $string
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[]
	 */
	private function getProductParameterValuesDataFromString($string) {
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

			if (!isset($this->parameters[$serializedParameterNames])) {
				$parameterNames = $this->unserializeLocalizedValues($serializedParameterNames);
				$parameter = $this->parameterFacade->findParameterByNames($parameterNames);
				if ($parameter === null) {
					$parameter = $this->parameterFacade->create(new ParameterData($parameterNames));
				}
				$this->parameters[$serializedParameterNames] = $parameter;
			}

			$valueTexts = $this->unserializeLocalizedValues($serializedValueTexts);
			foreach ($valueTexts as $locale => $valueText) {
				$productParameterValueData = new ProductParameterValueData();
				$productParameterValueData->parameter = $this->parameters[$serializedParameterNames];
				$productParameterValueData->locale = $locale;
				$productParameterValueData->valueText = $valueText;
				$productParameterValuesData[] = $productParameterValueData;
			}
		}

		return $productParameterValuesData;
	}

	/**
	 * @param string $string
	 * @return array
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
	 * @param string $string
	 * @param array $productData
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	private function getProductDataFromString($string, array $productData) {
		$data = [];
		if (!empty($string)) {
			$ids = explode(';', $string);
			foreach ($ids as $id) {
				$data[] = $productData[$id];
			}
		}

		return $data;
	}
}
