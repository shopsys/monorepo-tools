# Extending Product List

If you want add more information to product lists, you need to do a little bit more than [extending an entity](/docs/cookbook/adding-new-attribute-to-an-entity.md).
That is because the frontend product lists leverage [the read model concept](/docs/model/introduction-to-read-model.md), i.e. special view objects are used instead of common Doctrine entities in the Twig templates.

This cookbook describes how to extend the frontend product lists with step by step instructions.

## Display a brand name in a product view on the product list

### 1. Extend `ListedProductView` class and add a new attribute.

The class encapsulates the data that are needed for displaying products on FE product lists.
We want to display a brand name for each product so we need to add the attribute to the class.

```php
declare(strict_types=1);

namespace Shopsys\ShopBundle\Model\Product\View;

use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice;
use Shopsys\ReadModelBundle\Image\ImageView;
use Shopsys\ReadModelBundle\Product\Action\ProductActionView;
use Shopsys\ReadModelBundle\Product\Listed\ListedProductView as BaseListedProductView;

class ListedProductView extends BaseListedProductView
{
    /**
     * @var string|null
     */
    protected $brandName;

    /**
     * @param int $id
     * @param string $name
     * @param string|null $shortDescription
     * @param string $availability
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice $sellingPrice
     * @param array $flagIds
     * @param \Shopsys\ReadModelBundle\Product\Action\ProductActionView $action
     * @param \Shopsys\ReadModelBundle\Image\ImageView|null $image
     * @param string|null $brandName
     */
    public function __construct(
        int $id,
        string $name,
        ?string $shortDescription,
        string $availability,
        ProductPrice $sellingPrice,
        array $flagIds,
        ProductActionView $action,
        ?ImageView $image,
        ?string $brandName
    ) {
        parent::__construct($id, $name, $shortDescription, $availability, $sellingPrice, $flagIds, $action, $image);

        $this->brandName = $brandName;
    }

    /**
     * @return string|null
     */
    public function getBrandName(): ?string
    {
        return $this->brandName;
    }
}
```

### 2. Add new attribute to Elasticsearch

In order to add new attribute to Elasticsearch you need to add it to the structure first.
You can do that by adding it to `mappings` in all `src/Shopsys/ShopBundle/Resources/definition/product/*.json` files like this:
```diff
  "mappings": {
    "_doc": {
      "properties": {
+       "brand_name": {
+         "type": "text"
+       },
```

### 3. Export new attribute to Elasticsearch

The class responsible for exporting products to Elasticsearch is `ProductSearchExportWithFilterRepository` so we need to extend it and add the attribute to method `getProductsData`.
```php
declare(strict_types=1);

namespace Shopsys\ShopBundle\Model\Product\Search\Export;

use Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportWithFilterRepository as BaseProductSearchExportWithFilterRepository;

class ProductSearchExportWithFilterRepository extends BaseProductSearchExportWithFilterRepository
{
    /**
     * @param int $domainId
     * @param string $locale
     * @param int $startFrom
     * @param int $batchSize
     * @return array
     */
    public function getProductsData(int $domainId, string $locale, int $startFrom, int $batchSize): array
    {
        $queryBuilder = $this->createQueryBuilder($domainId, $locale)
            ->setFirstResult($startFrom)
            ->setMaxResults($batchSize);

        $query = $queryBuilder->getQuery();

        $result = [];
        /** @var \Shopsys\FrameworkBundle\Model\Product\Product $product */
        foreach ($query->getResult() as $product) {
            $flagIds = $this->extractFlags($product);
            $categoryIds = $this->extractCategories($domainId, $product);
            $parameters = $this->extractParameters($locale, $product);
            $prices = $this->extractPrices($domainId, $product);
            $visibility = $this->extractVisibility($domainId, $product);

            $friendlyUrl = $this->friendlyUrlRepository->getMainFriendlyUrl($domainId, 'front_product_detail', $product->getId());

            $result[] = [
                'id' => $product->getId(),
                'catnum' => $product->getCatnum(),
                'partno' => $product->getPartno(),
                'ean' => $product->getEan(),
                'name' => $product->getName($locale),
                'description' => $product->getDescription($domainId),
                'shortDescription' => $product->getShortDescription($domainId),
                'brand' => $product->getBrand() ? $product->getBrand()->getId() : '',
                'brand_name' => $product->getBrand() ? $product->getBrand()->getName() : '',
                'flags' => $flagIds,
                'categories' => $categoryIds,
                'in_stock' => $product->getCalculatedAvailability()->getDispatchTime() === 0,
                'prices' => $prices,
                'parameters' => $parameters,
                'ordering_priority' => $product->getOrderingPriority(),
                'calculated_selling_denied' => $product->getCalculatedSellingDenied(),
                'selling_denied' => $product->isSellingDenied(),
                'availability' => $product->getAvailability()->getName($locale),
                'main_variant' => $product->isMainVariant(),
                'detail_url' => $friendlyUrl->getAbsoluteUrl($this->domain),
                'visibility' => $visibility,
            ];
        }

        return $result;
    }
}
```

You need to register your new class as an alias for the one from the FrameworkBundle in `services.yml` and `services_test.yml`:

```yml
Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportWithFilterRepository: '@Shopsys\ShopBundle\Model\Product\Search\Export\ProductSearchExportWithFilterRepository'
```

Then you need to fix `ProductSearchExportRepositoryTest::getExpectedStructureForRepository` (because this test check if your structure is correct) by adding new attribute:
```diff
$structure = \array_merge($structure, [
    'availability',
    'brand',
    'flags',
    'categories',
    'detail_url',
    'in_stock',
    'prices',
    'parameters',
    'ordering_priority',
    'calculated_selling_denied',
    'selling_denied',
    'main_variant',
    'visibility',
+   'brand_name',
]);
```

### 4. Extend `ListedProductViewFactory` so it returns the new required data

The class is responsible for creating the view object. We need to ensure that the objects is created with proper brand name. We are able to get the brand name from the product entity, so we just need to overwrite `createFromArray()` and `createFromProduct()` methods.  

```php
declare(strict_types=1);

namespace Shopsys\ShopBundle\Model\Product\View;

use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\ReadModelBundle\Image\ImageView;
use Shopsys\ReadModelBundle\Product\Action\ProductActionView;
use Shopsys\ReadModelBundle\Product\Listed\ListedProductViewFactory as BaseListedProductViewFactory;
use Shopsys\ReadModelBundle\Product\Listed\ListedProductView as BaseListedProductView;

class ListedProductViewFactory extends BaseListedProductViewFactory
{
    /**
     * @param array $productArray
     * @param \Shopsys\ReadModelBundle\Image\ImageView|null $imageView
     * @param \Shopsys\ReadModelBundle\Product\Action\ProductActionView $productActionView
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\ShopBundle\Model\Product\ViewListedProductView
     */
    public function createFromArray(array $productArray, ?ImageView $imageView, ProductActionView $productActionView, PricingGroup $pricingGroup): BaseListedProductView
    {
        return new ListedProductView(
            $productArray['id'],
            $productArray['name'],
            $productArray['shortDescription'],
            $productArray['availability'],
            $this->getProductPriceFromArrayByPricingGroup($productArray['prices'], $pricingGroup),
            $productArray['flags'],
            $productActionView,
            $imageView,
            $productArray['brand_name']
        );
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param \Shopsys\ReadModelBundle\Image\ImageView|null $imageView
     * @param \Shopsys\ReadModelBundle\Product\Action\ProductActionView $productActionView
     * @return \Shopsys\ShopBundle\Model\Product\View\ListedProductView
     */
    public function createFromProduct(Product $product, ?ImageView $imageView, ProductActionView $productActionView): BaseListedProductView
    {
        return new ListedProductView(
            $product->getId(),
            $product->getName(),
            $product->getShortDescription($this->domain->getId()),
            $product->getCalculatedAvailability()->getName(),
            $this->productCachedAttributesFacade->getProductSellingPrice($product),
            $this->getFlagIdsForProduct($product),
            $productActionView,
            $imageView,
            $product->getBrand() !== null ? $product->getBrand()->getName() : null
        );
    }
}
```

You need to register your new class as an alias for the one from the bundle in `services.yml`:

```yml
Shopsys\ReadModelBundle\Product\Listed\ListedProductViewFactory: '@Shopsys\ShopBundle\Model\Product\View\ListedProductViewFactory'
```

### 5. Modify the frontend template for rendering product lists so it displays the new attribute

All product lists are rendered using `productListMacro.html.twig`. You can modify this macro to display product brand name wherever it is suitable for you.

```diff
{# src/Shopsys/ShopBundle/Resources/views/Front/Content/Product/productListMacro.html.twig #}

<p class="list-products__item__info__description">
+    {{ productView.brandName }}
+    <br>
    {{ productView.shortDescription }}
</p>
```
