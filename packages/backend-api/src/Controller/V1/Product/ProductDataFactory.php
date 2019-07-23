<?php

declare(strict_types=1);

namespace Shopsys\BackendApiBundle\Controller\V1\Product;

use Ramsey\Uuid\Uuid;
use Shopsys\BackendApiBundle\Component\DataSetter\ApiDataSetter;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductData;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface as BaseProductDataFactoryInterface;

/**
 * @experimental
 */
class ProductDataFactory implements ProductDataFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface
     */
    protected $productDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade
     */
    protected $availabilityFacade;

    /**
     * @var \Shopsys\BackendApiBundle\Component\DataSetter\ApiDataSetter
     */
    protected $apiDataSetter;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface $productDataFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade $availabilityFacade
     * @param \Shopsys\BackendApiBundle\Component\DataSetter\ApiDataSetter $apiDataSetter
     */
    public function __construct(
        BaseProductDataFactoryInterface $productDataFactory,
        AvailabilityFacade $availabilityFacade,
        ApiDataSetter $apiDataSetter
    ) {
        $this->productDataFactory = $productDataFactory;
        $this->availabilityFacade = $availabilityFacade;
        $this->apiDataSetter = $apiDataSetter;
    }

    /**
     * @param array $productApiData
     * @param string|null $uuid
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductData
     */
    public function createFromApi(array $productApiData, ?string $uuid = null): ProductData
    {
        $productData = $this->productDataFactory->create();
        $productData->uuid = $uuid ?: Uuid::uuid4()->toString();
        $productData->availability = $this->availabilityFacade->getDefaultInStockAvailability();

        $this->setProductDataByApi($productData, $productApiData);

        return $productData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param array $productApiData
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductData
     */
    public function createFromProductAndApi(Product $product, array $productApiData): ProductData
    {
        $productData = $this->productDataFactory->createFromProduct($product);
        $this->setProductDataByApi($productData, $productApiData);
        return $productData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     * @param array $productApiData
     */
    protected function setProductDataByApi(ProductData $productData, array $productApiData): void
    {
        $this->apiDataSetter->setValueIfExists('hidden', $productApiData, $productData);
        $this->apiDataSetter->setValueIfExists('sellingDenied', $productApiData, $productData);
        $this->apiDataSetter->setDateTimeValueIfExists('sellingFrom', $productApiData, $productData);
        $this->apiDataSetter->setDateTimeValueIfExists('sellingTo', $productApiData, $productData);
        $this->apiDataSetter->setValueIfExists('catnum', $productApiData, $productData);
        $this->apiDataSetter->setValueIfExists('ean', $productApiData, $productData);
        $this->apiDataSetter->setValueIfExists('partno', $productApiData, $productData);
        $this->apiDataSetter->setMultilanguageValueIfExists('name', $productApiData, $productData);
        $this->apiDataSetter->setMultidomainValueIfExists('shortDescription', $productApiData, $productData, 'shortDescriptions');
        $this->apiDataSetter->setMultidomainValueIfExists('longDescription', $productApiData, $productData, 'descriptions');
    }
}
