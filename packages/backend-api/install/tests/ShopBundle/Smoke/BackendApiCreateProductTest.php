<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Smoke;

use Ramsey\Uuid\Uuid;
use Tests\ShopBundle\Test\OauthTestCase;

/**
 * This test must not extend TransactionFunctionalTestCase because it mustn't use transaction
 * If it is run in transaction, tokens don't work and test fails
 */
class BackendApiCreateProductTest extends OauthTestCase
{
    public function testCreatePostProduct(): void
    {
        $product = $this->getValidProduct();
        $response = $this->runOauthRequest('POST', '/api/v1/products', $product);

        $this->assertSame(201, $response->getStatusCode());

        $location = $response->headers->get('Location');
        $this->assertProductLocation($location);

        $uuid = $this->extractUuid($location);

        $foundProduct = $this->getProduct($uuid);
        $product['uuid'] = $uuid;
        $this->assertEquals($product, $foundProduct);
    }

    public function testCreateProductWithSpecifiedUuid(): void
    {
        $product = $this->getValidProduct();
        $uuid = Uuid::uuid4()->toString();
        $product['uuid'] = $uuid;
        $response = $this->runOauthRequest('POST', '/api/v1/products', $product);

        $this->assertSame(201, $response->getStatusCode());

        $location = $response->headers->get('Location');
        $this->assertProductLocation($location);

        $extractedUuid = $this->extractUuid($location);
        $this->assertSame($uuid, $extractedUuid);

        $foundProduct = $this->getProduct($extractedUuid);
        $this->assertEquals($product, $foundProduct);
    }

    public function testCreateProductWithWoringUuid(): void
    {
        $product = $this->getValidProduct();
        $product['uuid'] = 'xxx';
        $response = $this->runOauthRequest('POST', '/api/v1/products', $product);

        $this->assertSame(400, $response->getStatusCode());
    }

    public function testCreatePutProductWithAlreadyExistingUuid(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $product = $this->getValidProduct();
        $product['uuid'] = $uuid;
        $response = $this->runOauthRequest('POST', '/api/v1/products', $product);

        $this->assertSame(201, $response->getStatusCode());

        $response = $this->runOauthRequest('POST', '/api/v1/products', $product);

        $this->assertSame(422, $response->getStatusCode());
    }

    /**
     * @param string $location
     */
    private function assertProductLocation(string $location): void
    {
        $expectedLocationStart = sprintf('%s/api/v1/products/', $this->getDomainBaseUrl());
        $this->assertStringStartsWith($expectedLocationStart, $location);
    }

    /**
     * @param string $uuid
     * @return array
     */
    private function getProduct(string $uuid): array
    {
        $response = $this->runOauthRequest('GET', '/api/v1/products/' . $uuid);
        $this->assertSame(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        return $data;
    }

    public function testCreateEmptyProduct(): void
    {
        $product = [];
        $response = $this->runOauthRequest('POST', '/api/v1/products', $product);

        $this->assertSame(201, $response->getStatusCode());

        $location = $response->headers->get('Location');
        $this->assertProductLocation($location);

        $uuid = $this->extractUuid($location);

        $expected = [
            'uuid' => $uuid,
            'name' => [
                'en' => null,
                'cs' => null,
            ],
            'hidden' => false,
            'sellingDenied' => false,
            'sellingFrom' => null,
            'sellingTo' => null,
            'catnum' => null,
            'ean' => null,
            'partno' => null,
            'shortDescription' => [
                1 => null,
                2 => null,
            ],
            'longDescription' => [
                1 => null,
                2 => null,
            ],
        ];
        $actualProduct = $this->getProduct($uuid);
        $this->assertEquals($expected, $actualProduct);
    }

    public function testValidationError(): void
    {
        $product = [
            'name' => [
                'en' => 'name longer than 255 letters Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam mollis erat turpis, ac ullamcorper tellus tempor a. Sed dapibus posuere dui sed iaculis. Phasellus non magna et urna aliquam fringilla et sit amet diam. Suspendisse suscipit lacus quis nisi sed.',
                'xx' => 'Some wrong text',
            ],
            'hidden' => 'false',
            'sellingDenied' => 'false',
            'sellingFrom' => 123,
            'sellingTo' => '12.1.2019',
            'catnum' => 'CAT12346B Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas viverra orci nec diam turpis duis.',
            'ean' => 'E12346B Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas viverra orci nec diam turpis duis.',
            'partno' => 'P123456 Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas viverra orci nec diam turpis duis.',
            'shortDescription' => [
                1 => 123,
                2 => '<b>popisek',
                99 => 'not existing domain',
            ],
            'longDescription' => [
                1 => 345,
                2 => '<b>popisek dlouhy',
                99 => 'not existing domain',
            ],
        ];
        $response = $this->runOauthRequest('POST', '/api/v1/products', $product);

        $this->assertSame(400, $response->getStatusCode());

        $expectedResponse = [
            'code' => 400,
            'message' => 'Provided data did not pass validation',
            'errors' => [
                'name.en' => 'The value "name longer than 255 letters Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam mollis erat turpis, ac ullamcorper tellus tempor a. Sed dapibus posuere dui sed iaculis. Phasellus non magna et urna aliquam fringilla et sit amet diam. Suspendisse suscipit lacus quis nisi sed." cannot be longer then 255 characters',
                'name.xx' => 'This field was not expected.',
                'hidden' => 'The value "false" is not a valid bool.',
                'sellingDenied' => 'The value "false" is not a valid bool.',
                'sellingFrom' => 'The value "123" is not a valid DateTime::ATOM format.',
                'sellingTo' => 'The value "12.1.2019" is not a valid DateTime::ATOM format.',
                'catnum' => 'The value "CAT12346B Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas viverra orci nec diam turpis duis." cannot be longer then 100 characters',
                'ean' => 'The value "E12346B Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas viverra orci nec diam turpis duis." cannot be longer then 100 characters',
                'partno' => 'The value "P123456 Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas viverra orci nec diam turpis duis." cannot be longer then 100 characters',
                'shortDescription.1' => 'The value 123 is not a valid string.',
                'shortDescription.99' => 'This field was not expected.',
                'longDescription.1' => 'The value 345 is not a valid string.',
                'longDescription.99' => 'This field was not expected.',
            ],
        ];

        $this->assertSame($expectedResponse, json_decode($response->getContent(), true));
    }

    /**
     * @return array
     */
    private function getValidProduct(): array
    {
        $product = [
            'name' => [
                'en' => 'X tech',
                'cs' => 'název',
            ],
            'hidden' => true,
            'sellingDenied' => true,
            'sellingFrom' => '2019-07-16T00:00:00+00:00',
            'sellingTo' => '2020-07-16T00:00:00+00:00',
            'catnum' => '123456 co',
            'ean' => 'E12346B',
            'partno' => 'P123456',
            'shortDescription' => [
                1 => '<b>desc',
                2 => '<b>popisek',
            ],
            'longDescription' => [
                1 => '<b>desc long',
                2 => '<b>popisek dlouhy',
            ],
        ];

        return $product;
    }
}
