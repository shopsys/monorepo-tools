<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use DateTime;
use SS6\ShopBundle\Component\Csv\CsvDecoder;
use SS6\ShopBundle\Component\Csv\CsvReader;
use SS6\ShopBundle\Component\String\EncodingConverter;
use SS6\ShopBundle\Component\String\TransformString;
use SS6\ShopBundle\Model\Product\Parameter\Parameter;
use SS6\ShopBundle\Model\Product\Parameter\ParameterData;
use SS6\ShopBundle\Model\Product\Parameter\ParameterValue;
use SS6\ShopBundle\Model\Product\Parameter\ParameterValueData;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData;
use SS6\ShopBundle\Model\Product\ProductData;

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
	 * @var array
	 */
	private $departments;

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
	public function injectReferences(array $vats, array $availabilities, array $departments) {
		$this->vats = $vats;
		$this->availabilities = $availabilities;
		$this->departments = $departments;
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
		$productData->setNames(['cs' => $row[0], 'en' => $row[1]]);
		$productData->setCatnum($row[2]);
		$productData->setPartno($row[3]);
		$productData->setEan($row[4]);
		$productData->setDescriptions(['cs' => $row[5], 'en' => $row[6]]);
		$productData->setPrice($row[7]);
		switch ($row[8]) {
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
		if ($row[9] !== null) {
			$productData->setSellingFrom(new DateTime($row[9]));
		}
		if ($row[10] !== null) {
			$productData->setSellingTo(new DateTime($row[10]));
		}
		$productData->setStockQuantity($row[11]);
		$hiddenOnDomains = array();
		if (!CsvDecoder::decodeBoolean($row[12])) {
			$hiddenOnDomains[] = 1;
		}
		if (!CsvDecoder::decodeBoolean($row[13])) {
			$hiddenOnDomains[] = 2;
		}
		$productData->setHiddenOnDomains($hiddenOnDomains);
		switch ($row[14]) {
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
		$productData->setParameters($this->getProductParameterValuesDataFromString($row[15]));
		$productData->setDepartments($this->getProductDepartmentsFromString($row[16]));

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

			list($serializedParameterNames, $valueText) = $rowData;
			$serializedParameterNames = trim($serializedParameterNames, '[]');

			if (!isset($this->parameters[$serializedParameterNames])) {
				$parameterNames = $this->parseParameterNames($serializedParameterNames);
				$this->parameters[$serializedParameterNames] = new Parameter(new ParameterData($parameterNames));
			}

			if (!isset($this->parameterValues[$valueText])) {
				$this->parameterValues[$valueText] = new ParameterValue(new ParameterValueData($valueText));
			}

			$productParameterValueData = new ProductParameterValueData();
			$productParameterValueData->setParameter($this->parameters[$serializedParameterNames]);
			$productParameterValueData->setValue($this->parameterValues[$valueText]);
			$productParameterValuesData[] = $productParameterValueData;
		}

		return $productParameterValuesData;
	}

	/**
	 * @param string $string
	 * @return \SS6\ShopBundle\Model\Department\Department[]
	 */
	private function parseParameterNames($string) {
		$parameterNames = array();
		$sections = explode(',', $string);
		foreach ($sections as $section) {
			$row = explode(':', $section);
			$parameterNames[$row[0]] = $row[1];
		}
		return $parameterNames;
	}

	/**
	 * @param string $string
	 * @return \SS6\ShopBundle\Model\Department\Department[]
	 */
	private function getProductDepartmentsFromString($string) {
		$departments = array();
		if (!empty($string)) {
			$departmentIds = explode(';', $string);
			foreach ($departmentIds as $departmentId) {
				$departments[] = $this->departments[$departmentId];
			}
		}

		return $departments;
	}
}
