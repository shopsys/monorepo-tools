<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Smoke;

use DateTime;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Tests\ShopBundle\Test\OauthTestCase;
use Webmozart\Assert\Assert;

/**
 * This test must not extend TransactionFunctionalTestCase because it mustn't use transaction
 * If it is run in transaction, tokens don't work and test fails
 */
class BackendApiTest extends OauthTestCase
{
    public function testProductsReturnsArray(): void
    {
        $response = $this->runOauthRequest('GET', '/api/v1/products');

        $this->assertSame(200, $response->getStatusCode());
        $jsonContent = json_decode($response->getContent(), true);
        $this->assertCount(100, $jsonContent);
        foreach ($jsonContent as $product) {
            $this->assertProductJsonStructure($product);
        }
    }

    public function testProductsReturnsLinksInHeaders(): void
    {
        $firstPageResponse = $this->runOauthRequest('GET', '/api/v1/products');

        $firstPageLink = $firstPageResponse->headers->get('Link');
        $this->assertStringContainsString('api/v1/products?page=2', $firstPageLink);
        $this->assertStringContainsString('rel="next"', $firstPageLink);
        $this->assertStringContainsString('rel="last"', $firstPageLink);

        $secondPageResponse = $this->runOauthRequest('GET', '/api/v1/products?page=2');

        $secondPageLink = $secondPageResponse->headers->get('Link');
        $this->assertStringContainsString('api/v1/products?page=1', $secondPageLink);
        $this->assertStringContainsString('rel="prev"', $secondPageLink);
        $this->assertStringContainsString('rel="first"', $secondPageLink);
    }

    public function testProductReturnsArray(): void
    {
        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');

        $uri = sprintf('/api/v1/products/%s', $product->getUuid());
        $response = $this->runOauthRequest('GET', $uri);

        $this->assertSame(200, $response->getStatusCode());
        $jsonContent = json_decode($response->getContent(), true);
        $this->assertProductJsonStructure($jsonContent);
    }

    /**
     * @param array $product
     */
    private function assertProductJsonStructure(array $product): void
    {
        Assert::uuid($product['uuid']);
        $this->assertIsArray($product['name']);
        $this->assertIsString($product['name']['en']);
        $this->assertIsBool($product['hidden']);
        $this->assertIsBool($product['sellingDenied']);
        $this->nullOrStringDatetimeInAtomFormat($product['sellingFrom']);
        $this->nullOrStringDatetimeInAtomFormat($product['sellingTo']);
        $this->assertIsString($product['catnum']);
        $this->assertIsString($product['ean']);
        Assert::nullOrString($product['partno']);
        $this->assertIsArray($product['shortDescription']);
        Assert::nullOrString($product['shortDescription'][1]);
        $this->assertIsArray($product['longDescription']);
        Assert::nullOrString($product['longDescription'][1]);
    }

    /**
     * @param string|null $datetime
     */
    private function nullOrStringDatetimeInAtomFormat(?string $datetime): void
    {
        if ($datetime === null) {
            return;
        }

        $datetime = DateTime::createFromFormat(DateTime::ATOM, $datetime);
        $this->assertInstanceOf(DateTime::class, $datetime);
    }
}
