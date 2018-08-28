# Adding New Attribute to an Entity
In the following example, we will add `extId` (alias "external ID") field to the `Product` entity.
It is a common modification when you need your e-commerce application and ERP system to co-work smoothly.

## Extend framework `Product` entity

*Note: How does the entity extension work? Find it out in the [separate article](../wip_glassbox/entity-extension.md). Most common entitties (including `Product`) are already extended in `project-base` to ease your development. However, when extending any other entity, there are [few more steps](../wip_glassbox/entity-extension.md#how-can-i-extend-an-entity) that need to be done.*

1. Add new `extId` field with Doctrine ORM annotations and a getter for the field into `Shopsys\ShopBundle\Model\Product\Product` class.

1. Overwrite constructor for creating `Product` instances.
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
        protected function __construct(BaseProductData $productData, $variants = null)
        {
            $this->extId = $productData->extId ?? 0;
            parent::__construct($productData, $variants);
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

1. Generate a [database migration](../introduction/database-migrations.md) creating a new column for the field by running:
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

1. Run the migration to actually create the column in your database:
    ```
    php phing db-migrations
    ```

1. Add public `extId` field into `Shopsys\ShopBundle\Model\Product\ProductData` class.
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

1. Edit `Shopsys\ShopBundle\Model\Product\ProductDataFactory` - overwrite `create()` and `createFromProduct()` methods.
    *Alternatively you can create an independent class by implementing
[`Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface`](../../packages/framework/src/Model/Product/ProductDataFactoryInterface.php).*

    ```php
    <?php

    namespace Shopsys\ShopBundle\Model\Product;

    use Shopsys\FrameworkBundle\Model\Product\Product as BaseProduct;
    use Shopsys\FrameworkBundle\Model\Product\ProductData as BaseProductData;
    use Shopsys\FrameworkBundle\Model\Product\ProductDataFactory as BaseProductDataFactory;

    class ProductDataFactory extends BaseProductDataFactory
    {
        /**
         * @param \Shopsys\ShopBundle\Model\Product\Product $product
         * @return \Shopsys\ShopBundle\Model\Product\ProductData
         */
        public function createFromProduct(BaseProduct $product): BaseProductData
        {
            $productData = new ProductData();
            $this->fillFromProduct($productData, $product);
            $productData->extId = $product->getExtId() ?? 0;
         
            return $productData;
        }

        /**
         * @return \Shopsys\ShopBundle\Model\Product\ProductData
         */
        public function create(): BaseProductData
        {
            $productData = new ProductData();
            $this->fillNew($productData);
            $productData->extId = 0;

            return $productData;
        }
    }
    ```

Your `ProductDataFactory` is already registered in [`services.yml`](../../project-base/src/Shopsys/ShopBundle/Resources/config/services.yml) 
as an alias for the original interface.
```
Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface: '@Shopsys\ShopBundle\Model\Product\ProductDataFactory'
```

## Enable administrator to edit the `extId` field
1. Add your `extId` field into the form by editing `ProductFormTypeExtension` in `Shopsys\ShopBundle\Form\Admin` namespace.
The original `ProductFormType` is set as the extended type by implementation of `getExtendedType()` method.
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
1. In your `Product` class, overwrite the `edit()` method.
    ```php
    <?php

    namespace Shopsys\ShopBundle\Model\Product;

    use Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface;
    use Shopsys\FrameworkBundle\Model\Product\ProductData as BaseProductData;

    // ...

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
     * @param \Shopsys\ShopBundle\Model\Product\ProductData $productData
     */
    public function edit(ProductCategoryDomainFactoryInterface $productCategoryDomainFactory, BaseProductData $productData)
    {
        $this->extId = $productData->extId;
        parent::edit($productCategoryDomainFactory, $productData);
    }
    ```

1. In your `ProductDataFactory` class, update the `createFromProduct()` method so it sets your new `extId` field.

    ```php
    <?php

    namespace Shopsys\ShopBundle\Model\Product;

    use Shopsys\FrameworkBundle\Model\Product\Product;
    use Shopsys\FrameworkBundle\Model\Product\ProductData as BaseProductData;

    // ...

    class ProductDataFactory extends BaseProductDataFactory
    {
        /**
         * @param \Shopsys\ShopBundle\Model\Product\Product $product
         * @return \Shopsys\ShopBundle\Model\Product\ProductData
         */
        public function createFromProduct(BaseProduct $product): BaseProductData
        {
            $productData = new ProductData();
            $this->fillFromProduct($productData, $product);
            $productData->extId = $product->getExtId();

            return $productData;
        }

        // ...
    }
    ```

## Front-end
In order to display your new attribute on a front-end page, you can modify the corresponding template directly 
as it is a part of your open-box, eg. [`detail.html.twig`](../../project-base/src/Shopsys/ShopBundle/Resources/views/Front/Content/Product/detail.html.twig).
```
{{ product.extId }}
```

## Tests
You need to fix your tests to reflect new changes:
* Instances of `Product` and `ProductData` are often created directly in tests - change all of them to your classes.
* In [`ProductVisibilityRepositoryTest`](../../project-base/tests/ShopBundle/Database/Model/Product/ProductVisibilityRepositoryTest.php), 
instance of `ProductData` is created directly. 
so you need to use there your class:
```php
<?php

namespace Tests\ShopBundle\Database\Model\Product;

use Shopsys\ShopBundle\Model\Product\ProductData;

class ProductVisibilityRepositoryTest extends DatabaseTestCase
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductData
     */
    private function getDefaultProductData()
    {
        // ...

        $productData = new ProductData();
        
        // ...
    }
}
```
* You also need to fix smoke test for creating new product as your new `extId` is required attribute.
In [`NewProductTest`](../../project-base/tests/ShopBundle/Smoke/NewProductTest.php) 
add following line to the `fillForm()` method:
```php
$form['product_form[extId]'] = 123456;
```

## Data fixtures
Currently, it is not possible to modify data fixtures from your project, this issue will be addressed in near future.
