# Adding New Attribute to an Entity
In the following example, we will add `extId` (alias "external ID") field to the `Product` entity.
It is a common modification when you need your e-commerce application and ERP system to co-work smoothly.

*Note: if you want to display your new attribute on the frontend product list, you need to extend the [read model layer](/docs/model/introduction-to-read-model.md) as well.
You can find instructions in [Extending Product List](./cookbook/extending-product-list.md).*

## Extend framework `Product` entity

*Note: How does the entity extension work? Find it out in the [separate article](../extensibility/entity-extension.md). Most common entities (including `Product`) are already extended in `project-base` to ease your development. However, when extending any other entity, there are [few more steps](../extensibility/entity-extension.md#how-can-i-extend-an-entity) that need to be done.*

1. Add new `extId` field with Doctrine ORM annotations and a getter for the field into `Shopsys\ShopBundle\Model\Product\Product` class.

1. Overwrite constructor for creating `Product` instances.
    ```php
    namespace Shopsys\ShopBundle\Model\Product;

    use Doctrine\ORM\Mapping as ORM;
    use Shopsys\FrameworkBundle\Model\Product\Product as BaseProduct;
    use Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface;
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
         * @param \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
         * @param \Shopsys\ShopBundle\Model\Product\Product[]|null $variants
         */
        protected function __construct(BaseProductData $productData, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory, array $variants = null)
        {
            parent::__construct($productData, $productCategoryDomainFactory, $variants);

            $this->extId = $productData->extId ?? 0;
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

    *Note: In this step you were using Phing target `db-migrations-generate`.
    More information about what Phing targets are and how they work can be found in [Console Commands for Application Management (Phing Targets)](/docs/introduction/console-commands-for-application-management-phing-targets.md)*

1. Run the migration to actually create the column in your database:
    ```
    php phing db-migrations
    ```

1. Add public `extId` field into `Shopsys\ShopBundle\Model\Product\ProductData` class.
    ```php
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
    for `Product` and `ProductData` instantiation to make them take our new attribute into account.

1. Edit `Shopsys\ShopBundle\Model\Product\ProductDataFactory` - overwrite `create()` and `createFromProduct()` methods.
    *Alternatively you can create an independent class by implementing
[`Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface`](../../packages/framework/src/Model/Product/ProductDataFactoryInterface.php).*

    ```php
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
            // "basicInformationGroup" is defined in ProductFormType
            $basicInformationGroup = $builder->get('basicInformationGroup');
            $basicInformationGroup->add('extId', IntegerType::class, [
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
    *Note: If you want to change order for your newly created field, please look at section [Changing order of groups and fields](https://github.com/shopsys/shopsys/blob/master/docs/extensibility/form-extension.md#changing-order-of-groups-and-fields)*
1. In your `Product` class, overwrite the `edit()` method.
    ```php
    namespace Shopsys\ShopBundle\Model\Product;

    use Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface;
    use Shopsys\FrameworkBundle\Model\Product\ProductData as BaseProductData;
    use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;

    // ...

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
     * @param \Shopsys\ShopBundle\Model\Product\ProductData $productData
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler $productPriceRecalculationScheduler
     */
    public function edit(
        ProductCategoryDomainFactoryInterface $productCategoryDomainFactory,
        BaseProductData $productData,
        ProductPriceRecalculationScheduler $productPriceRecalculationScheduler
    ) {
        parent::edit($productCategoryDomainFactory, $productData, $productPriceRecalculationScheduler);

        $this->extId = $productData->extId;
    }
    ```

1. In your `ProductDataFactory` class, update the `createFromProduct()` method so it sets your new `extId` field.

    ```php
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

## Data fixtures
You can modify data fixtures in `src/Shopsys/ShopBundle/DataFixtures/` of your project
### Random `extId`
If you want to add unique random `extId` for products from data fixtures you can add it in `createProductDataFromRowForFirstDomain` method of [`ProductDataFixtureLoader.php`](../../project-base/src/Shopsys/ShopBundle/DataFixtures/Demo/ProductDataFixtureLoader.php).
You can use [`Faker`](https://github.com/fzaninotto/Faker) to generate random numbers like this:
```diff
+   use Faker\Generator as Faker;

    //...

+   /**
+    * @var \Faker\Generator
+    */
+   private $faker;

    /**
     * @param \Shopsys\ShopBundle\DataFixtures\Demo\ProductParametersFixtureLoader $productParametersFixtureLoader
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface $productDataFactory
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupFacade
+    * @param \Faker\Generator $faker  
     */
    public function __construct(
        ProductParametersFixtureLoader $productParametersFixtureLoader,
        ProductDataFactoryInterface $productDataFactory,
        Domain $domain,
-       PricingGroupFacade $pricingGroupFacade
+       PricingGroupFacade $pricingGroupFacade,
+       Faker $faker
    ) {
        $this->productParametersFixtureLoader = $productParametersFixtureLoader;
        $this->productDataFactory = $productDataFactory;
        $this->domain = $domain;
        $this->pricingGroupFacade = $pricingGroupFacade;
+       $this->faker = $faker;  
    }

    //...

    public function createProductDataFromRowForFirstDomain($row)
        {
            $productData = $this->productDataFactory->create();
            $this->updateProductDataFromCsvRowForFirstDomain($productData, $row);
+           $productData->extId = $this->faker->unique()->numberBetween(1, 10000);

            return $productData;
        }
```

### Specific `extId`
If you need to add specific `extId` to products in data fixture you can add new column to [`demo-data-products.csv`](../../project-base/src/Shopsys/ShopBundle/DataFixtures/resources/demo-data-products.csv).
Then you have to set the value of the new column in `updateProductDataFromCsvRowForFirstDomain` method of [`ProductDataFixtureLoader.php`](../../project-base/src/Shopsys/ShopBundle/DataFixtures/Demo/ProductDataFixtureLoader.php).
```diff
+   const COLUMN_EXT_ID = 24;  

    //...

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     * @param array $row
     */
    protected function updateProductDataFromCsvRowForFirstDomain(ProductData $productData, array $row)
    {

        //...

+       $productData->extId = $row[self::COLUMN_EXT_ID];
    }  
```
