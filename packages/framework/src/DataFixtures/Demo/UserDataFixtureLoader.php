<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Shopsys\FrameworkBundle\Component\Csv\CsvReader;
use Shopsys\FrameworkBundle\Component\String\EncodingConverter;
use Shopsys\FrameworkBundle\Component\String\TransformString;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddressDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Customer\CustomerDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Customer\UserDataFactory;

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
     * @var \Shopsys\FrameworkBundle\Model\Customer\UserDataFactory
     */
    private $userDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\Country[]
     */
    private $countries;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerDataFactoryInterface
     */
    private $customerDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\BillingAddressDataFactoryInterface
     */
    private $billingAddressDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressDataFactoryInterface
     */
    private $deliveryAddressDataFactory;

    /**
     * @param string $path
     * @param \Shopsys\FrameworkBundle\Component\Csv\CsvReader $csvReader
     * @param \Shopsys\FrameworkBundle\Model\Customer\UserDataFactory $userDataFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerDataFactoryInterface $customerDataFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddressDataFactoryInterface $billingAddressDataFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressDataFactoryInterface $deliveryAddressDataFactory
     */
    public function __construct(
        $path,
        CsvReader $csvReader,
        UserDataFactory $userDataFactory,
        CustomerDataFactoryInterface $customerDataFactory,
        BillingAddressDataFactoryInterface $billingAddressDataFactory,
        DeliveryAddressDataFactoryInterface $deliveryAddressDataFactory
    ) {
        $this->path = $path;
        $this->csvReader = $csvReader;
        $this->userDataFactory = $userDataFactory;
        $this->customerDataFactory = $customerDataFactory;
        $this->billingAddressDataFactory = $billingAddressDataFactory;
        $this->deliveryAddressDataFactory = $deliveryAddressDataFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\Country[] $countries
     */
    public function injectReferences(array $countries)
    {
        $this->countries = $countries;
    }

    /**
     * @param int $domainId
     * @return  \Shopsys\FrameworkBundle\Model\Customer\CustomerData[]
     */
    public function getCustomersDataByDomainId($domainId)
    {
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
    private function filterRowsByDomainId(array $rows, $domainId)
    {
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
     * @return \Shopsys\FrameworkBundle\Model\Customer\CustomerData
     */
    private function getCustomerDataFromCsvRow(array $row)
    {
        $customerData = $this->customerDataFactory->create();
        $domainId = (int)$row[self::COLUMN_DOMAIN_ID];
        $userData = $this->userDataFactory->createDefault($domainId);
        $billingAddressData = $this->billingAddressDataFactory->create();

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
            $deliveryAddressData = $this->deliveryAddressDataFactory->create();
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
            $customerData->deliveryAddressData = $this->deliveryAddressDataFactory->create();
        }
        $userData->domainId = $domainId;

        $customerData->userData = $userData;
        $customerData->billingAddressData = $billingAddressData;

        return $customerData;
    }

    /**
     * @param string $countryName
     * @return \Shopsys\FrameworkBundle\Model\Country\Country
     */
    private function getCountryByName($countryName)
    {
        foreach ($this->countries as $country) {
            if ($country->getName() === $countryName) {
                return $country;
            }
        }

        $message = 'Country with name "' . $countryName . '" was not found.';
        throw new \Shopsys\FrameworkBundle\Model\Country\Exception\CountryNotFoundException($message);
    }
}
