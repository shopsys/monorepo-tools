<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use DateTime;
use SS6\ShopBundle\Component\Csv\CsvDecoder;
use SS6\ShopBundle\Component\Csv\CsvReader;
use SS6\ShopBundle\Component\String\EncodingConverter;
use SS6\ShopBundle\Component\String\TransformString;
use SS6\ShopBundle\Model\Product\Parameter\Parameter;
use SS6\ShopBundle\Model\Product\Parameter\ParameterData;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Product\ProductEditData;

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
	 * @return \SS6\ShopBundle\Model\Product\ProductEditData[]
	 */
	public function getProductsEditData() {
		$rows = $this->csvReader->getRowsFromCsv($this->path);

		$rowId = 0;
		foreach ($rows as $row) {
			if ($rowId !== 0) {
				$row = array_map(array(TransformString::class, 'emptyToNull'), $row);
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
		$productEditData->productData->setName(['cs' => $row[0], 'en' => $row[1]]);
		$productEditData->productData->setCatnum($row[2]);
		$productEditData->productData->setPartno($row[3]);
		$productEditData->productData->setEan($row[4]);
		$productEditData->productData->setDescription(['cs' => $row[5], 'en' => $row[6]]);
		$productEditData->productData->setPrice($row[7]);
		switch ($row[8]) {
			case 'high':
				$productEditData->productData->setVat($this->vats['high']);
				break;
			case 'low':
				$productEditData->productData->setVat($this->vats['low']);
				break;
			case 'zero':
				$productEditData->productData->setVat($this->vats['zero']);
				break;
			default:
				$productEditData->productData->setVat(null);
		}
		if ($row[9] !== null) {
			$productEditData->productData->setSellingFrom(new DateTime($row[9]));
		}
		if ($row[10] !== null) {
			$productEditData->productData->setSellingTo(new DateTime($row[10]));
		}
		$productEditData->productData->setStockQuantity($row[11]);
		$hiddenOnDomains = array();
		if (!CsvDecoder::decodeBoolean($row[12])) {
			$hiddenOnDomains[] = 1;
		}
		if (!CsvDecoder::decodeBoolean($row[13])) {
			$hiddenOnDomains[] = 2;
		}
		$productEditData->productData->setHiddenOnDomains($hiddenOnDomains);
		switch ($row[14]) {
			case 'in-stock':
				$productEditData->productData->setAvailability($this->availabilities['in-stock']);
				break;
			case 'out-of-stock':
				$productEditData->productData->setAvailability($this->availabilities['out-of-stock']);
				break;
			case 'on-request':
				$productEditData->productData->setAvailability($this->availabilities['on-request']);
				break;
			default:
				$productEditData->productData->setAvailability(null);
		}
		$productEditData->parameters = $this->getProductParameterValuesDataFromString($row[15]);
		$productEditData->productData->setDepartments($this->getProductDepartmentsFromString($row[16]));

		return $productEditData;
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

			list($serializedParameterNames, $serializedValueTexts) = $rowData;
			$serializedParameterNames = trim($serializedParameterNames, '[]');
			$serializedValueTexts = trim($serializedValueTexts, '[]');

			if (!isset($this->parameters[$serializedParameterNames])) {
				$parameterNames = $this->unserializeLocalizedValues($serializedParameterNames);
				$this->parameters[$serializedParameterNames] = new Parameter(new ParameterData($parameterNames));
			}

			$valueTexts = $this->unserializeLocalizedValues($serializedValueTexts);
			foreach ($valueTexts as $locale => $valueText) {
				$productParameterValueData = new ProductParameterValueData();
				$productParameterValueData->setParameter($this->parameters[$serializedParameterNames]);
				$productParameterValueData->setLocale($locale);
				$productParameterValueData->setValueText($valueText);
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
		$array = array();
		$items = explode(',', $string);
		foreach ($items as $item) {
			list($locale, $value) = explode(':', $item);
			$array[$locale] = $value;
		}
		return $array;
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
