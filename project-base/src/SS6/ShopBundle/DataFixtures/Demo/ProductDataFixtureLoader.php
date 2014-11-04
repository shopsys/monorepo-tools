<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use DateTime;
use SS6\ShopBundle\Component\Csv\CsvDecoder;
use SS6\ShopBundle\Component\Csv\CsvReader;
use SS6\ShopBundle\Model\Product\Parameter\Parameter;
use SS6\ShopBundle\Model\Product\Parameter\ParameterData;
use SS6\ShopBundle\Model\Product\Parameter\ParameterValue;
use SS6\ShopBundle\Model\Product\Parameter\ParameterValueData;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\String\EncodingConverter;
use SS6\ShopBundle\Model\String\TransformString;

class ProductDataFixtureLoader {

	/**
	 * @var CsvReader
	 */
	private $csvReader;

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
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterValue[]
	 */
	private $parameterValues;

	/**
	 * @param string $path
	 * @param \SS6\ShopBundle\Component\Csv\CsvReader $csvReader
	 */
	public function __construct($path, CsvReader $csvReader) {
		$this->path = $path;
		$this->csvReader = $csvReader;
	}

	/**
	 * @param array $vats
	 * @param array $availabilities
	 */
	public function injectReferences(array $vats, array $availabilities) {
		$this->vats = $vats;
		$this->availabilities = $availabilities;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\ProductData[]
	 */
	public function getProductsData() {
		$rows = $this->csvReader->getRowsFromCsv($this->path);

		$rowId = 0;
		foreach ($rows as $row) {
			if ($rowId !== 0) {
				$row = array_map(array(TransformString::class, 'emptyToNull'), $row);
				$row = EncodingConverter::cp1250ToUtf8($row);
				$productsData[] = $this->getProductDataFromCsvRow($row);
			}
			$rowId++;
		}
		return $productsData;
	}

	/**
	 * @param array $row
	 * @return \SS6\ShopBundle\Model\Product\ProductData
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	private function getProductDataFromCsvRow(array $row) {
		$productData = new ProductData();
		$productData->setName($row[0]);
		$productData->setCatnum($row[1]);
		$productData->setPartno($row[2]);
		$productData->setEan($row[3]);
		$productData->setDescription($row[4]);
		$productData->setPrice($row[5]);
		switch ($row[6]) {
			case 'high':
				$productData->setVat($this->vats['high']);
				break;
			case 'low':
				$productData->setVat($this->vats['low']);
				break;
			case 'zero':
				$productData->setVat($this->vats['zero']);
				break;
			default:
				$productData->setVat(null);
		}
		if ($row[7] !== null) {
			$productData->setSellingFrom(new DateTime($row[7]));
		}
		if ($row[8] !== null) {
			$productData->setSellingTo(new DateTime($row[8]));
		}
		$productData->setStockQuantity($row[9]);
		$showOnDomains = array();
		if (CsvDecoder::decodeBoolean($row[10])) {
			$showOnDomains[] = 1;
		}
		if (CsvDecoder::decodeBoolean($row[11])) {
			$showOnDomains[] = 2;
		}
		$productData->setShowOnDomains($showOnDomains);
		switch ($row[12]) {
			case 'in-stock':
				$productData->setAvailability($this->availabilities['in-stock']);
				break;
			case 'out-of-stock':
				$productData->setAvailability($this->availabilities['out-of-stock']);
				break;
			case 'on-request':
				$productData->setAvailability($this->availabilities['on-request']);
				break;
			default:
				$productData->setAvailability(null);
		}
		$productData->setParameters($this->getProductParameterValuesDataFromString($row[13]));

		return $productData;
	}

	/**
	 * @param string $string
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[]
	 */
	private function getProductParameterValuesDataFromString($string) {
		$rows = explode(';', $string);

		$productParameterValuesData = array();
		foreach ($rows as $row) {
			$rowData = explode('=', $row);
			if (count($rowData) !== 2) {
				continue;
			}

			list($parameterName, $valueText) = $rowData;

			if (!isset($this->parameters[$parameterName])) {
				$this->parameters[$parameterName] = new Parameter(new ParameterData($parameterName));
			}

			if (!isset($this->parameterValues[$valueText])) {
				$this->parameterValues[$valueText] = new ParameterValue(new ParameterValueData($valueText));
			}

			$productParameterValueData = new ProductParameterValueData();
			$productParameterValueData->setParameter($this->parameters[$parameterName]);
			$productParameterValueData->setValue($this->parameterValues[$valueText]);
			$productParameterValuesData[] = $productParameterValueData;
		}

		return $productParameterValuesData;
	}
}
