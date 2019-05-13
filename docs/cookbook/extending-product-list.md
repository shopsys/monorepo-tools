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

### 2. Extend `ListedProductViewFactory` so it returns the new required data

The class is responsible for creating the view object. We need to ensure that the objects is created with proper brand name. We are able to get the brand name from the product entity, so we just need to overwrite `createFromProduct()` method.  

```php
declare(strict_types=1);

namespace Shopsys\ShopBundle\Model\Product\View;

use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\ReadModelBundle\Image\ImageView;
use Shopsys\ReadModelBundle\Product\Action\ProductActionView;
use Shopsys\ReadModelBundle\Product\Listed\ListedProductViewFactory as BaseListedProductViewFactory;
use Shopsys\ReadModelBundle\Product\Listed\ListedProductView as BaseListedProductView;

class ListedProductViewFactory extends BaseListedProductViewFactory
{
    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param \Shopsys\ReadModelBundle\Image\ImageView|null $imageView
     * @param \Shopsys\ReadModelBundle\Product\Action\ProductActionView $productActionView
     * @return \Shopsys\ReadModelBundle\Product\Listed\ListedProductView
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
            $product->getBrand() ? $product->getBrand()->getName() : null
        );
    }
}
```

You need to register your new class as an alias for the one from the bundle in `services.yml`:

```yml
Shopsys\ReadModelBundle\Product\Listed\ListedProductViewFactory: '@Shopsys\ShopBundle\Model\Product\View\ListedProductViewFactory'
```

### 3. Modify the frontend template for rendering product lists so it displays the new attribute

All product lists are rendered using `productListMacro.html.twig`. You can modify this macro to display product brand name wherever it is suitable for you.

```diff
{# src/Shopsys/ShopBundle/Resources/views/Front/Content/Product/productListMacro.html.twig #}

<p class="list-products__item__info__description">
+    {{ productView.brandName }}
+    <br>
    {{ productView.shortDescription }}
</p>
```
