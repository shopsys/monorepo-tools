<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use SS6\ShopBundle\Model\Csv\CsvReader;
use SS6\ShopBundle\Model\Customer\BillingAddressData;
use SS6\ShopBundle\Model\Customer\CustomerData;
use SS6\ShopBundle\Model\Customer\DeliveryAddressData;
use SS6\ShopBundle\Model\Customer\UserData;
use SS6\ShopBundle\Model\String\TransformString;
use SS6\ShopBundle\Model\String\EncodingConverter;

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
	 * @return  \SS6\ShopBundle\Model\Customer\CustomerData[]
	 */
	public function getCustomersData() {
		$rows = $this->csvReader->getRowsFromCsv($this->path);

		$rowId = 0;
		foreach ($rows as $row) {
			if ($rowId !== 0) {
				$row = array_map(array(TransformString::class, 'emptyToNull'), $row);
				$row = EncodingConverter::cp1250ToUtf8($row);
				$customersData[] = $this->getCustomerDataFromCsvRow($row);
			}
			$rowId++;
		}
		return $customersData;
	}

	/**
	 * @param array $row
	 * @return \SS6\ShopBundle\Model\Customer\CustomerData
	 */
	private function getCustomerDataFromCsvRow(array $row) {
		$customerData = new CustomerData();
		$userData = new UserData();
		$billingAddressData = new BillingAddressData();

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
		$customerData->setDeliveryAddress(null);
		if ($row[12] === 'true') {
			$deliveryAddressData = new DeliveryAddressData();
			$deliveryAddressData->setAddressFilled(true);
			$deliveryAddressData->setCity($row[13]);
			$deliveryAddressData->setCompanyName($row[14]);
			$deliveryAddressData->setContactPerson($row[15]);
			$deliveryAddressData->setPostcode($row[16]);
			$deliveryAddressData->setStreet($row[17]);
			$deliveryAddressData->setTelephone($row[18]);
			$customerData->setDeliveryAddress($deliveryAddressData);
		}
		$userData->setDomainId($row[19]);

		$customerData->setUserData($userData);
		$customerData->setBillingAddress($billingAddressData);

		return $customerData;
	}
}
