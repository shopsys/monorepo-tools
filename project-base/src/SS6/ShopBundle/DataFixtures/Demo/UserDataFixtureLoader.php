<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use SS6\ShopBundle\Component\Csv\CsvReader;
use SS6\ShopBundle\Component\String\EncodingConverter;
use SS6\ShopBundle\Component\String\TransformString;
use SS6\ShopBundle\Model\Customer\BillingAddressData;
use SS6\ShopBundle\Model\Customer\CustomerData;
use SS6\ShopBundle\Model\Customer\DeliveryAddressData;
use SS6\ShopBundle\Model\Customer\UserDataFactory;

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
	 * @var \SS6\ShopBundle\Model\Customer\UserDataFactory
	 */
	private $userDataFactory;

	/**
	 * @param string $path
	 * @param \SS6\ShopBundle\Component\Csv\CsvReader $csvReader
	 * @param \SS6\ShopBundle\Model\Customer\UserDataFactory $userDataFactory
	 */
	public function __construct($path, CsvReader $csvReader, UserDataFactory $userDataFactory) {
		$this->path = $path;
		$this->csvReader = $csvReader;
		$this->userDataFactory = $userDataFactory;
	}

	/**
	 * @return  \SS6\ShopBundle\Model\Customer\CustomerData[]
	 */
	public function getCustomersData() {
		$rows = $this->csvReader->getRowsFromCsv($this->path);

		$rowId = 0;
		foreach ($rows as $row) {
			if ($rowId !== 0) {
				$row = array_map([TransformString::class, 'emptyToNull'], $row);
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
		$domainId = $row[19];
		$userData = $this->userDataFactory->createDefault($domainId);
		$billingAddressData = new BillingAddressData();

		$userData->firstName = $row[0];
		$userData->lastName = $row[1];
		$userData->email = $row[2];
		$userData->password = $row[3];

		$billingAddressData->companyCustomer = $row[4];
		$billingAddressData->companyName = $row[5];
		$billingAddressData->companyNumber = $row[6];
		$billingAddressData->companyTaxNumber = $row[7];
		$billingAddressData->street = $row[8];
		$billingAddressData->city = $row[9];
		$billingAddressData->postcode = $row[10];
		$billingAddressData->telephone = $row[11];
		$customerData->deliveryAddressData = null;
		if ($row[12] === 'true') {
			$deliveryAddressData = new DeliveryAddressData();
			$deliveryAddressData->addressFilled = true;
			$deliveryAddressData->city = $row[13];
			$deliveryAddressData->companyName = $row[14];
			$deliveryAddressData->contactPerson = $row[15];
			$deliveryAddressData->postcode = $row[16];
			$deliveryAddressData->street = $row[17];
			$deliveryAddressData->telephone = $row[18];
			$customerData->deliveryAddressData = $deliveryAddressData;
		}
		$userData->domainId = $domainId;

		$customerData->userData = $userData;
		$customerData->billingAddressData = $billingAddressData;

		return $customerData;
	}
}
