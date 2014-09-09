<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use SS6\ShopBundle\Model\Csv\CsvReader;
use SS6\ShopBundle\Model\Customer\UserData;
use SS6\ShopBundle\Model\String\TransformString;
use SS6\ShopBundle\Model\String\EncodingConvertor;
use SS6\ShopBundle\Model\Customer\BillingAddressData;
use SS6\ShopBundle\Model\Customer\DeliveryAddressData;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\DeliveryAddress;

class UserDataFixtureLoader {

	/**
	 * @var CsvReader
	 */
	private $csvReader;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @param string $path
	 * @param \SS6\ShopBundle\Model\Csv\CsvReader $csvReader
	 */
	public function __construct($path, CsvReader $csvReader) {
		$this->path = $path;
		$this->csvReader = $csvReader;
	}

	/**
	 * @return array
	 */
	public function getUsersData() {
		$rows = $this->csvReader->getRowsFromCsv($this->path);

		$rowId = 0;
		foreach ($rows as $row) {
			if ($rowId !== 0) {
				$row = array_map(array(TransformString::class, 'emptyStringsToNulls'), $row);
				$row = EncodingConvertor::cp1250ToUtf8($row);
				$usersData[] = $this->getUserDataFromCsvRow($row);
			}
			$rowId++;
		}
		return $usersData;
	}

	/**
	 * @param array $row
	 * @return array
	 */
	private function getUserDataFromCsvRow($row) {
		$userData = new UserData();
		$billingAddressData = new BillingAddressData();
		$deliveryAddressData = new DeliveryAddressData();

		$userData->setFirstName($row[0]);
		$userData->setLastName($row[1]);
		$userData->setEmail($row[2]);
		$userData->setPassword($row[3]);

		$billingAddressData->setCompanyCustomer($row[4]);
		$billingAddressData->setCompanyName($row[5]);
		$billingAddressData->setCompanyNumber($row[6]);
		$billingAddressData->setCompanyTaxNumber($row[7]);
		$billingAddressData->setStreet($row[8]);
		$billingAddressData->setCity($row[9]);
		$billingAddressData->setPostcode($row[10]);
		$billingAddressData->setTelephone($row[11]);
		$billingAddress = new BillingAddress($billingAddressData);

		if ($row[12] === 'true') {
			$deliveryAddressData->setAddressFilled(true);
			$deliveryAddressData->setCity($row[13]);
			$deliveryAddressData->setCompanyName($row[14]);
			$deliveryAddressData->setContactPerson($row[15]);
			$deliveryAddressData->setPostcode($row[16]);
			$deliveryAddressData->setStreet($row[17]);
			$deliveryAddressData->setTelephone($row[18]);
			$deliveryAddress = new DeliveryAddress($deliveryAddressData);
		} else {
			$deliveryAddressData->setAddressFilled(false);
			$deliveryAddress = null;
		}
		return array('user' => $userData, 'billing' => $billingAddress, 'delivery' => $deliveryAddress);
	}
}