<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use SS6\ShopBundle\Model\Csv\CsvReader;
use SS6\ShopBundle\Model\String\TransformString;
use SS6\ShopBundle\Model\String\EncodingConvertor;
use SS6\ShopBundle\Model\Product\ProductData;
use DateTime;

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
	 * @param string $path
	 * @param \SS6\ShopBundle\Model\Csv\CsvReader $csvReader
	 */
	public function __construct($path, CsvReader $csvReader) {
		$this->path = $path;
		$this->csvReader = $csvReader;
	}

	/**
	 * @param array $vats
	 * @param array $availabilities
	 */
	public function injectReferences($vats, $availabilities) {
		$this->vats = $vats;
		$this->availabilities = $availabilities;
	}

	/**
	 * @return array
	 */
	public function getProductsData() {
		$rows = $this->csvReader->getRowsFromCsv($this->path);

		$rowId = 0;
		foreach ($rows as $row) {
			if ($rowId !== 0) {
				$row = array_map(array(TransformString::class, 'emptyStringsToNulls'), $row);
				$row = EncodingConvertor::cp1250ToUtf8($row);
				$productsData[] = $this->getProductDataFromCsvRow($row);
			}
			$rowId++;
		}
		return $productsData;		
	}

	/**
	 * @param array $row
	 * @return \SS6\ShopBundle\Model\Product\ProductData
	 */
	private function getProductDataFromCsvRow($row) {
		$productData = new ProductData();
		$productData->setName($row[0]);
		$productData->setCatnum($row[1]);
		$productData->setPartno($row[2]);
		$productData->setEan($row[3]);
		$productData->setDescription($row[4]);
		$productData->setPrice($row[5]);
		switch ($row[6]) {
			case 'high': $productData->setVat($this->vats['high']);
				break;
			case 'low': $productData->setVat($this->vats['low']);
				break;
			case 'zero': $productData->setVat($this->vats['zero']);
				break;
		}
		$productData->setSellingFrom(new DateTime($row[7]));
		$productData->setSellingTo(new DateTime($row[8]));
		$productData->setStockQuantity($row[9]);
		$productData->setHidden($row[10]);
		switch ($row[11]) {
			case 'in-stock': $productData->setAvailability($this->availabilities['in-stock']);
				break;
			case 'out-of-stock': $productData->setAvailability($this->availabilities['out-of-stock']);
				break;
			case 'on-request': $productData->setAvailability($this->availabilities['on-request']);
				break;
			default : $productData->setAvailability(null);
		}
		return $productData;
	}
}