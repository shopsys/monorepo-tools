<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Shopsys\ShopBundle\Component\Csv\CsvReader;
use Shopsys\ShopBundle\Component\String\EncodingConverter;
use Shopsys\ShopBundle\Component\String\TransformString;
use Shopsys\ShopBundle\Model\Customer\BillingAddressData;
use Shopsys\ShopBundle\Model\Customer\CustomerData;
use Shopsys\ShopBundle\Model\Customer\DeliveryAddressData;
use Shopsys\ShopBundle\Model\Customer\UserDataFactory;

class UserDataFixtureLoader
{

    const COLUMN_FIRSTNAME = 0;
    const COLUMN_LASTNAME = 1;
    const COLUMN_EMAIL = 2;
    const COLUMN_PASSWORD = 3;
    const COLUMN_COMPANY_CUSTOMER = 4;
    const COLUMN_COMPANY_NAME = 5;
    const COLUMN_COMPANY_NUMBER = 6;
    const COLUMN_COMPANY_TAX_NUMBER = 7;
    const COLUMN_STREET = 8;
    const COLUMN_CITY = 9;
    const COLUMN_POSTCODE = 10;
    const COLUMN_TELEPHONE = 11;
    const COLUMN_COUNTRY = 12;
    const COLUMN_DELIVERY_ADDRESS_FILLED = 13;
    const COLUMN_DELIVERY_CITY = 14;
    const COLUMN_DELIVERY_COMPANY_NAME = 15;
    const COLUMN_DELIVERY_FIRST_NAME = 16;
    const COLUMN_DELIVERY_LAST_NAME = 17;
    const COLUMN_DELIVERY_POSTCODE = 18;
    const COLUMN_DELIVERY_STREET = 19;
    const COLUMN_DELIVERY_TELEPHONE = 20;
    const COLUMN_DELIVERY_COUNTRY = 21;
    const COLUMN_DOMAIN_ID = 22;

    /**
     * @var CsvReader
     */
    private $csvReader;

    /**
     * @var string
     */
    private $path;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\UserDataFactory
     */
    private $userDataFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Country\Country[]
     */
    private $countries;

    /**
     * @param string $path
     * @param \Shopsys\ShopBundle\Component\Csv\CsvReader $csvReader
     * @param \Shopsys\ShopBundle\Model\Customer\UserDataFactory $userDataFactory
     */
    public function __construct($path, CsvReader $csvReader, UserDataFactory $userDataFactory) {
        $this->path = $path;
        $this->csvReader = $csvReader;
        $this->userDataFactory = $userDataFactory;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Country\Country[] $countries
     */
    public function injectReferences(array $countries) {
        $this->countries = $countries;
    }

    /**
     * @param int $domainId
     * @return  \Shopsys\ShopBundle\Model\Customer\CustomerData[]
     */
    public function getCustomersDataByDomainId($domainId) {
        $rows = $this->csvReader->getRowsFromCsv($this->path);
        $filteredRows = $this->filterRowsByDomainId($rows, $domainId);

        $customersData = [];
        foreach ($filteredRows as $row) {
            $row = array_map([TransformString::class, 'emptyToNull'], $row);
            $row = EncodingConverter::cp1250ToUtf8($row);
            $customersData[] = $this->getCustomerDataFromCsvRow($row);
        }
        return $customersData;
    }

    /**
     * @param array $rows
     * @param int $domainId
     * @return array
     */
    private function filterRowsByDomainId(array $rows, $domainId) {
        $filteredRows = [];
        $rowId = 0;
        foreach ($rows as $row) {
            $rowId++;
            if ($rowId === 1) {
                // skip header
                continue;
            }

            if ((int)$row[self::COLUMN_DOMAIN_ID] !== $domainId) {
                // filter by domain ID
                continue;
            }

            $filteredRows[] = $row;
        }

        return $filteredRows;
    }

    /**
     * @param array $row
     * @return \Shopsys\ShopBundle\Model\Customer\CustomerData
     */
    private function getCustomerDataFromCsvRow(array $row) {
        $customerData = new CustomerData();
        $domainId = (int)$row[self::COLUMN_DOMAIN_ID];
        $userData = $this->userDataFactory->createDefault($domainId);
        $billingAddressData = new BillingAddressData();

        $userData->firstName = $row[self::COLUMN_FIRSTNAME];
        $userData->lastName = $row[self::COLUMN_LASTNAME];
        $userData->email = $row[self::COLUMN_EMAIL];
        $userData->password = $row[self::COLUMN_PASSWORD];

        $billingAddressData->companyCustomer = $row[self::COLUMN_COMPANY_CUSTOMER];
        $billingAddressData->companyName = $row[self::COLUMN_COMPANY_NAME];
        $billingAddressData->companyNumber = $row[self::COLUMN_COMPANY_NUMBER];
        $billingAddressData->companyTaxNumber = $row[self::COLUMN_COMPANY_TAX_NUMBER];
        $billingAddressData->street = $row[self::COLUMN_STREET];
        $billingAddressData->city = $row[self::COLUMN_CITY];
        $billingAddressData->postcode = $row[self::COLUMN_POSTCODE];
        $billingAddressData->telephone = $row[self::COLUMN_TELEPHONE];
        $billingAddressData->country = $this->getCountryByName($row[self::COLUMN_COUNTRY]);
        if ($row[self::COLUMN_DELIVERY_ADDRESS_FILLED] === 'true') {
            $deliveryAddressData = new DeliveryAddressData();
            $deliveryAddressData->addressFilled = true;
            $deliveryAddressData->city = $row[self::COLUMN_DELIVERY_CITY];
            $deliveryAddressData->companyName = $row[self::COLUMN_DELIVERY_COMPANY_NAME];
            $deliveryAddressData->firstName = $row[self::COLUMN_DELIVERY_FIRST_NAME];
            $deliveryAddressData->lastName = $row[self::COLUMN_DELIVERY_LAST_NAME];
            $deliveryAddressData->postcode = $row[self::COLUMN_DELIVERY_POSTCODE];
            $deliveryAddressData->street = $row[self::COLUMN_DELIVERY_STREET];
            $deliveryAddressData->telephone = $row[self::COLUMN_DELIVERY_TELEPHONE];
            $deliveryAddressData->country = $this->getCountryByName($row[self::COLUMN_DELIVERY_COUNTRY]);
            $customerData->deliveryAddressData = $deliveryAddressData;
        } else {
            $customerData->deliveryAddressData = new DeliveryAddressData();
        }
        $userData->domainId = $domainId;

        $customerData->userData = $userData;
        $customerData->billingAddressData = $billingAddressData;

        return $customerData;
    }

    /**
     * @param string $countryName
     * @return \Shopsys\ShopBundle\Model\Country\Country
     */
    private function getCountryByName($countryName) {
        foreach ($this->countries as $country) {
            if ($country->getName() === $countryName) {
                return $country;
            }
        }

        $message = 'Country with name "' . $countryName . '" was not found.';
        throw new \Shopsys\ShopBundle\Model\Country\Exception\CountryNotFoundException($message);
    }
}
