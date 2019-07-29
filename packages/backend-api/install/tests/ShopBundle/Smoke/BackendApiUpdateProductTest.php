<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Smoke;

use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Tests\ShopBundle\Test\OauthTestCase;

/**
 * This test must not extend TransactionFunctionalTestCase because it mustn't use transaction
 * If it is run in transaction, tokens don't work and test fails
 */
class BackendApiUpdateProductTest extends OauthTestCase
{
    public function testUpdateProductCompletely(): void
    {
        $uuid = $this->createProduct();

        $updateData = [
            'name' => [
                'en' => 'Y changed',
                'cs' => 'název changed',
            ],
            'hidden' => false,
            'sellingDenied' => false,
            'sellingFrom' => null,
            'sellingTo' => null,
            'catnum' => 'co changed',
            'ean' => 'E changed',
            'partno' => 'P changed',
            'shortDescription' => [
                1 => '<b>changed',
                2 => '<b>popisek changed',
            ],
            'longDescription' => [
                1 => '<b>desc changed',
                2 => '<b>popisek changed',
            ],
        ];

        $response = $this->runOauthRequest('PATCH', sprintf('/api/v1/products/%s', $uuid), $updateData);

        $this->assertSame(204, $response->getStatusCode());

        $foundProduct = $this->getProduct($uuid);
        $this->assertSame($updateData, $foundProduct);
    }

    public function testUpdateOnlySingleField(): void
    {
        $uuid = $this->createProduct();

        $updateData = [
            'name' => [
                'en' => 'Y changed',
                'cs' => 'název changed',
            ],
        ];

        $response = $this->runOauthRequest('PATCH', sprintf('/api/v1/products/%s', $uuid), $updateData);

        $this->assertSame(204, $response->getStatusCode());

        $expected = [
            'name' => [
                'en' => 'Y changed',
                'cs' => 'název changed',
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

        $foundProduct = $this->getProduct($uuid);
        $this->assertSame($expected, $foundProduct);
    }

    public function testUpdateNothingChangesNothing(): void
    {
        $uuid = $this->createProduct();
        $expected = $this->getProduct($uuid);

        $updateData = [];

        $response = $this->runOauthRequest('PATCH', sprintf('/api/v1/products/%s', $uuid), $updateData);

        $this->assertSame(204, $response->getStatusCode());

        $foundProduct = $this->getProduct($uuid);
        $this->assertSame($expected, $foundProduct);
    }

    public function testValidationError(): void
    {
        $uuid = $this->createProduct();

        $updateData = [
            'sellingFrom' => '2019-01-01',
        ];

        $response = $this->runOauthRequest('PATCH', sprintf('/api/v1/products/%s', $uuid), $updateData);

        $this->assertSame(400, $response->getStatusCode());
    }

    public function testCannotUpdateVariant(): void
    {
        $variant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '75');
        $uuid = $variant->getUuid();

        $response = $this->runOauthRequest('PATCH', sprintf('/api/v1/products/%s', $uuid), []);

        $this->assertSame(400, $response->getStatusCode());

        $message = json_decode($response->getContent(), true)['message'];
        $this->assertSame('cannot update/delete variant/main variant, this functionality is not supported yet', $message);
    }

    public function testCannotUpdateMainVariant(): void
    {
        $mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '149');
        $uuid = $mainVariant->getUuid();

        $response = $this->runOauthRequest('PATCH', sprintf('/api/v1/products/%s', $uuid), []);

        $this->assertSame(400, $response->getStatusCode());

        $message = json_decode($response->getContent(), true)['message'];
        $this->assertSame('cannot update/delete variant/main variant, this functionality is not supported yet', $message);
    }

    /**
     * @return string
     */
    private function createProduct(): string
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

        $response = $this->runOauthRequest('POST', '/api/v1/products', $product);

        $location = $response->headers->get('Location');
        return $this->extractUuid($location);
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
        unset($data['uuid']);
        return $data;
    }
}
