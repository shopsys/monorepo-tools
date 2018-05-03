# Adding New Attribute to an Entity
In the following example, we will add `extId` (alias "external ID") field to the `Product` entity.
It is a common modification when you need your e-commerce application and ERP system to co-work smoothly.

## Extend framework `Product` entity
1. Create new `Product` class in `Shopsys\ShopBundle\Model\Product` 
namespace by extending [`Shopsys\FrameworkBundle\Model\Product\Product`](../../packages/framework/src/Model/Product/Product.php) 
and keep the ORM table and entity annotations.

*Note: How does the entity extension work? Find it out in the [separate article](../wip_glassbox/entity-extension.md).*

2. Add new `extId` field with Doctrine ORM annotations and a getter for the field.

3. Overwrite constructor and static methods for creating `Product` instances.
```php
<?php

namespace Shopsys\ShopBundle\Model\Product;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Product\Product as BaseProduct;
use Shopsys\FrameworkBundle\Model\Product\ProductData as BaseProductData;

/**
 * @ORM\Table(name="products")
 * @ORM\Entity
 */
class Product extends BaseProduct
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $extId;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\ProductData $productData
     * @param \Shopsys\ShopBundle\Model\Product\Product[]|null $variants
     */
    public function __construct(BaseProductData $productData, $variants = null)
    {
        $this->extId = $productData->extId ?? 0;
        parent::__construct($productData, $variants);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\ProductData $productData
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    public static function create(BaseProductData $productData)
    {
        return new self($productData, null);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\ProductData $productData
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $variants
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    public static function createMainVariant(BaseProductData $productData, array $variants)
    {
        return new self($productData, $variants);
    }

    /**
     * @return int
     */
    public function getExtId(): int
    {
        return $this->extId;
    }
}
```

Notice that type hints and annotations of the methods do not match. 
This is on purpose - extended class must respect interface of its parent while annotation ensures proper IDE autocomplete.

4. Register your extended entity in *entity extension map* in [parameters_common.yml](../../project-base/app/config/parameters_common.yml):
```
parameters:  
  shopsys.entity_extension.map:
    Shopsys\FrameworkBundle\Model\Product\Product: Shopsys\ShopBundle\Model\Product\Product
```

5. Generate database migration creating a new column for the field by running:
```
php phing db-migrations-generate
```

The command prints a file name the migration was generated into: 
```text
Checking database schema...
Database schema is not satisfying ORM, a new migration was generated!
Migration file ".../src/Shopsys/ShopBundle/Migrations/Version20180503133713.php" was saved (525 B).
```

As you are adding not nullable field, you need to manually modify the generated migration
and add a default value for already existing entries:
```php
$this->sql('ALTER TABLE products ADD ext_id INT NOT NULL DEFAULT 0');
$this->sql('ALTER TABLE products ALTER ext_id DROP DEFAULT');
```

6. Run the migration to actually create the column in your database:
```
php phing db-migrations
```

7. Create new `ProductData` class in the same namespace as your `Product` entity
by extending [`Shopsys\FrameworkBundle\Model\Product\ProductData`](../../packages/framework/src/Model/Product/ProductData.php).
Add public `extId` field to the data object.
```php
<?php

namespace Shopsys\ShopBundle\Model\Product;

use Shopsys\FrameworkBundle\Model\Product\ProductData as BaseProductData;

class ProductData extends BaseProductData
{
    /**
     * @var int
     */
    public $extId;
}
```
In the following steps, we will overwrite all services that are responsible 
for `Product` and `ProductData` instantiation to make them return our extended classes.
 
8. Create new `ProductFacade` in the same namespace as your entity
by extending [`Shopsys\FrameworkBundle\Model\Product\ProductFacade`](../../packages/framework/src/Model/Product/ProductFacade.php)
and overwrite the `create()` method. Use your `Product` instead of the base one. 
The rest of the method can be copy pasted.
```php
<?php

namespace Shopsys\ShopBundle\Model\Product;

use Shopsys\FrameworkBundle\Model\Product\ProductEditData as BaseProductEditData;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade as BaseProductFacade;

class ProductFacade extends BaseProductFacade
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductEditData $productEditData
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    public function create(BaseProductEditData $productEditData)
    {
        $product = Product::create($productEditData->productData);

        // ... (copy paste from BaseProductFacade)

        return $product;
    }
}
```

Set your new `ProductFacade` for auto discovery in [`services.yml`](../../project-base/src/Shopsys/ShopBundle/Resources/config/services.yml) 
and register it as an alias for the original service.
```
Shopsys\ShopBundle\Model\:
  resource: '../../Model/*/*Facade.php'

Shopsys\FrameworkBundle\Model\Product\ProductFacade: '@Shopsys\ShopBundle\Model\Product\ProductFacade'
```

9. Create new `ProductDataFactory` in the same namespace as your entity
by extending [`Shopsys\FrameworkBundle\Model\Product\ProductDataFactory`](../../packages/framework/src/Model/Product/ProductDataFactory.php)
and overwrite the `createDefault()` method. 
You need to copy paste the contents of the parent class and make sure to use your `Product` instead of the base one.
```php
<?php

namespace Shopsys\ShopBundle\Model\Product;

use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductInputPriceFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactory as BaseProductDataFactory;
use Shopsys\FrameworkBundle\Model\Product\Unit\UnitFacade;

class ProductDataFactory extends BaseProductDataFactory
{
    // ... (copy paste from BaseProductDataFactory)

    /**
     * @return \Shopsys\ShopBundle\Model\Product\ProductData
     */
    public function createDefault()
    {
        $productData = new ProductData();

        // ... (copy paste from BaseProductDataFactory)

        return $productData;
    }
}
```

Set your new `ProductDataFactory` for auto discovery in [`services.yml`](../../project-base/src/Shopsys/ShopBundle/Resources/config/services.yml) 
and register it as an alias for the original service.
```
Shopsys\ShopBundle\Model\:
  resource: '../../Model/*/*{Facade,Factory}.php'
  
...

Shopsys\FrameworkBundle\Model\Product\ProductDataFactory: '@Shopsys\ShopBundle\Model\Product\ProductDataFactory'
```

10. Create new `ProductVariantService` in the same namespace as your entity
by extending [`Shopsys\FrameworkBundle\Model\Product\ProductVariantService`](../../packages/framework/src/Model/Product/ProductVariantService.php)
and overwrite the `createMainVariant()` method.
Make sure to use your `Product` instead of the base one. 
```php
<?php

namespace Shopsys\ShopBundle\Model\Product;

use Shopsys\FrameworkBundle\Model\Product\Product as BaseProduct;
use Shopsys\FrameworkBundle\Model\Product\ProductEditData as BaseProductEditData;
use Shopsys\FrameworkBundle\Model\Product\ProductVariantService as BaseProductVariantService;

class ProductVariantService extends BaseProductVariantService
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductEditData $mainVariantEditData
     * @param \Shopsys\ShopBundle\Model\Product\Product $mainProduct
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $variants
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    public function createMainVariant(BaseProductEditData $mainVariantEditData, BaseProduct $mainProduct, array $variants)
    {
        $variants[] = $mainProduct;

        return Product::createMainVariant($mainVariantEditData->productData, $variants);
    }
}
``` 

Set your new `ProductVariantService` for auto discovery in [`services.yml`](../../project-base/src/Shopsys/ShopBundle/Resources/config/services.yml) 
and register it as an alias for the original service.
```
Shopsys\ShopBundle\Model\:
  resource: '../../Model/*/*{Facade,Factory,Service}.php'
  
...

Shopsys\FrameworkBundle\Model\Product\ProductVariantService: '@Shopsys\ShopBundle\Model\Product\ProductVariantService'
```

## Enable administrator to edit the `extId` field
1. Add your `extId` field into the form by creating new `ProductFormTypeExtension` in `Shopsys\ShopBundle\Form\Admin` namespace.
Set the original `ProductFormType` as the extended type by implementing the `getExtendedType()` method.
```php
<?php

namespace Shopsys\ShopBundle\Form\Admin;

use Shopsys\FrameworkBundle\Form\Admin\Product\ProductFormType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class ProductFormTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('extId', IntegerType::class, [
            'required' => true,
            'constraints' => [
                new Constraints\NotBlank(['message' => 'Please enter external ID']),
            ],
            'label' => 'External ID',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ProductFormType::class;
    }
}
```

2. Register `ProductFormTypeExtension` in [`forms.yml`](../../project-base/src/Shopsys/ShopBundle/Resources/config/forms.yml).
```
Shopsys\ShopBundle\Form\Admin\ProductFormTypeExtension:
  tags:
    - { name: form.type_extension, extended_type: Shopsys\FrameworkBundle\Form\Admin\Product\ProductFormType }
```

3. In your `Product` class, overwrite the `edit()` method.
```php
/**
 * @param \Shopsys\ShopBundle\Model\Product\ProductData $productData
 */
public function edit(ProductData $productData)
{
    $this->extId = $productData->extId;
    parent::edit($productData);
}
```

4. In your `ProductDataFactory` class, overwrite the `createFromProduct()` method.
You need to copy paste the parent's method content and on top of it, set your new `extId` field.
```php
/**
 * @param \Shopsys\ShopBundle\Model\Product\Product $product
 * @return \Shopsys\ShopBundle\Model\Product\ProductData
 */
public function createFromProduct(Product $product)
{
    $productData = $this->createDefault();

    // ... (copy paste from BaseProductDataFactory)
    $productData->extId = $product->getExtId();

    return $productData;
}
```

## Tests
You need to fix your tests to reflect new changes:
* Instances of `Product` and `ProductData` are often created directly in tests - change all of them to your classes.
* `ProductFacade` is accessed directly from container in tests 
so it must be declared public in test environment - add an alias for it in [`services_test.yml`](../../project-base/src/Shopsys/ShopBundle/Resources/config/services_test.yml)
and it will do the trick:
```
services:
  _defaults:
    autowire: true
    autoconfigure: true
    public: true

  ... 
  
  Shopsys\FrameworkBundle\Model\Product\ProductFacade: '@Shopsys\ShopBundle\Model\Product\ProductFacade'
```

* In [`ProductVisibilityRepositoryTest`](../../project-base/tests/ShopBundle/Database/Model/Product/ProductVisibilityRepositoryTest.php), 
instance of `ProductEditData` is created directly. It creates new instance of `ProductData` internally, 
so you need to use there your class:
```php
<?php

namespace Tests\ShopBundle\Database\Model\Product;

use Shopsys\ShopBundle\Model\Product\ProductData;

class ProductVisibilityRepositoryTest extends DatabaseTestCase
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductEditData
     */
    private function getDefaultProductEditData()
    {
        // ...

        $productEditData = new ProductEditData();
        $productEditData->productData = new ProductData();
        
        // ...
    }
}
```
* You also need to fix smoke test for creating new product as your new `extId` is required attribute.
In [`NewProductTest`](../../project-base/tests/ShopBundle/Smoke/NewProductTest.php) 
add following line to the `fillForm()` method:
```php
$form['product_edit_form[productData][extId]'] = 123456;
```