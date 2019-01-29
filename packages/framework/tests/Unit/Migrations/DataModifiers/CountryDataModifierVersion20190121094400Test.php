<?php

namespace Tests\FrameworkBundle\Unit\Migrations\DataModifiers;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Migrations\DataModifiers\CountryDataModifierVersion20190121094400;

class CountryDataModifierVersion20190121094400Test extends TestCase
{
    protected $data = [
        [
            'id' => 2,
            'name' => 'Slovakia',
            'domain_id' => 1,
            'code' => 'SK',
        ],
        [
            'id' => 3,
            'name' => 'Česká republika',
            'domain_id' => 2,
            'code' => 'CZ',
        ],
        [
            'id' => 4,
            'name' => 'Slovenská republika',
            'domain_id' => 2,
            'code' => 'SK',
        ],
        [
            'id' => 1,
            'name' => 'Czech republic',
            'domain_id' => 1,
            'code' => 'CZ',
        ],
        [
            'id' => 5,
            'name' => 'Morocco',
            'domain_id' => 1,
            'code' => 'MA',
        ],
        [
            'id' => 6,
            'name' => 'Uzbekistán',
            'domain_id' => 2,
            'code' => 'UB',
        ],
    ];

    public function testNewIdCodePair(): void
    {
        $transform = new CountryDataModifierVersion20190121094400($this->data);

        $expected = ['SK' => 2, 'CZ' => 1, 'MA' => 5, 'UB' => 6];

        $this->assertEquals($expected, $transform->getNewIdCodePair());
    }

    public function testGetAllIds(): void
    {
        $transform = new CountryDataModifierVersion20190121094400($this->data);

        $expected = [2 => 2, 3 => 3, 4 => 4, 1 => 1, 5 => 5, 6 => 6];

        $this->assertEquals($expected, $transform->getAllIds());
    }

    public function testGetNewIdForDomainAndCountryId(): void
    {
        $transform = new CountryDataModifierVersion20190121094400($this->data);

        $this->assertEquals(2, $transform->getNewId(4));
        $this->assertEquals(2, $transform->getNewId(2));
        $this->assertEquals(1, $transform->getNewId(3));
        $this->assertEquals(1, $transform->getNewId(1));
        $this->assertEquals(5, $transform->getNewId(5));
        $this->assertEquals(6, $transform->getNewId(6));
    }

    public function testGetDomainData(): void
    {
        $transform = new CountryDataModifierVersion20190121094400($this->data);

        $expected = [
            'country_id' => 1,
            'domain_id' => 1,
            'enabled' => true,
            'priority' => 0,
        ];
        $this->assertEquals($expected, $transform->getDomainDataForCountry(1, 'CZ'));

        $expected = [
            'country_id' => 1,
            'domain_id' => 2,
            'enabled' => true,
            'priority' => 0,
        ];
        $this->assertEquals($expected, $transform->getDomainDataForCountry(2, 'CZ'));

        $expected = [
            'country_id' => 5,
            'domain_id' => 1,
            'enabled' => true,
            'priority' => 0,
        ];
        $this->assertEquals($expected, $transform->getDomainDataForCountry(1, 'MA'));

        $expected = [
            'country_id' => 5,
            'domain_id' => 2,
            'enabled' => false,
            'priority' => 0,
        ];
        $this->assertEquals($expected, $transform->getDomainDataForCountry(2, 'MA'));

        $expected = [
            'country_id' => 6,
            'domain_id' => 1,
            'enabled' => false,
            'priority' => 0,
        ];
        $this->assertEquals($expected, $transform->getDomainDataForCountry(1, 'UB'));

        $expected = [
            'country_id' => 6,
            'domain_id' => 2,
            'enabled' => true,
            'priority' => 0,
        ];
        $this->assertEquals($expected, $transform->getDomainDataForCountry(2, 'UB'));
    }

    public function testGetCodes(): void
    {
        $transform = new CountryDataModifierVersion20190121094400($this->data);

        $this->assertEquals(['SK', 'CZ', 'MA', 'UB'], $transform->getAllCodes());
    }

    public function testGetTranslatableData(): void
    {
        $transform = new CountryDataModifierVersion20190121094400($this->data);

        $expected = [
            'translatable_id' => 1,
            'name' => 'Česká republika',
        ];
        $this->assertEquals($expected, $transform->getTranslatableDataForCountry(2, 'CZ'));

        $expected = [
            'translatable_id' => 1,
            'name' => 'Czech republic',
        ];
        $this->assertEquals($expected, $transform->getTranslatableDataForCountry(1, 'CZ'));

        $expected = [
            'translatable_id' => 2,
            'name' => 'Slovenská republika',
        ];
        $this->assertEquals($expected, $transform->getTranslatableDataForCountry(2, 'SK'));

        $expected = [
            'translatable_id' => 2,
            'name' => 'Slovakia',
        ];
        $this->assertEquals($expected, $transform->getTranslatableDataForCountry(1, 'SK'));

        $expected = [
            'translatable_id' => 5,
            'name' => 'MA',
        ];
        $this->assertEquals($expected, $transform->getTranslatableDataForCountry(2, 'MA'));

        $expected = [
            'translatable_id' => 5,
            'name' => 'Morocco',
        ];
        $this->assertEquals($expected, $transform->getTranslatableDataForCountry(1, 'MA'));

        $expected = [
            'translatable_id' => 6,
            'name' => 'Uzbekistán',
        ];
        $this->assertEquals($expected, $transform->getTranslatableDataForCountry(2, 'UB'));

        $expected = [
            'translatable_id' => 6,
            'name' => 'UB',
        ];
        $this->assertEquals($expected, $transform->getTranslatableDataForCountry(1, 'UB'));
    }

    public function testGetObsoleteIds(): void
    {
        $transform = new CountryDataModifierVersion20190121094400($this->data);

        $this->assertEquals([3, 4], $transform->getObsoleteCountryIds());
    }
}
