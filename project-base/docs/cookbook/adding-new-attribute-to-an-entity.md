# Adding new attribute to an entity

In the following example we will add `code` field to the `Product`  entity.

1. Open [`\Shopsys\ShopBundle\Model\Product\Product`](../../src/Shopsys/ShopBundle/Model/Product/Product.php) class and add new private field with mapping:
    ```php
    class Product
    {
        /**
         * @var string|null
         *
         * @ORM\Column(type="string", length=30, nullable=true)
         */
        private $code;
    }
    ```
    
    As product code is not mandatory it is defined as `nullable`.

2. Add getter for the new field:
    ```php
    class Product
    {
        /**
         * @return string|null
         */
        public function getCode()
        {
            return $this->code;
        }
    }
    ```

3. Generate database migration creating a new column for the field by running:
    ```
    phing db-migrations-generate
    ```
    
    The command prints a file name the migration was generated into: 
    ```text
    Checking database schema...
    Database schema is not satisfying ORM, it was generated a new migration!
    Migration file ".../src/Shopsys/ShopBundle/Migrations/Version20170203205403.php" was saved (489 B).
    ```

4. Run the migration to actually create the column in your database:
    ```
    phing db-migrations
    ```

## Enable administrator to edit the `code` field
1. Open [`\Shopsys\ShopBundle\Model\Product\ProductData`](../../src/Shopsys/ShopBundle/Model/Product/ProductData.php) data object and add public field named `code`:
    ```php
    class ProductData {
        
        /**
         * @var string|null
         */
        public $code;
        
    }
    ```

2. Now, you need to add assignment from data object's `code` field to the entity in constructor and `Product::edit()` method.
    ```php
    class Product {
        
        /**
         * @param \Shopsys\ShopBundle\Model\Product\ProductData $productData
         * @param \Shopsys\ShopBundle\Model\Product\Product[]|null $variants
         */
        private function __construct(ProductData $productData, array $variants = null) {
            // ...
            $this->code = $productData->code;
        }
        
        /**
         * @param \Shopsys\ShopBundle\Model\Product\ProductData
         */
        public function edit(ProductData $productData) {
            // ...
            $this->code = $productData->code;
        }
        
    }
    ```

3. Also, open [`\Shopsys\ShopBundle\Model\Product\ProductDataFactory`](../../src/Shopsys/ShopBundle/Model/Product/ProductDataFactory.php) and edit `createFromProduct()` method that fills `ProductData` from `Product` entity:
    ```php
    class ProductDataFactory
    {
        /**
         * @param \Shopsys\ShopBundle\Model\Product\Product $product
         * @return \Shopsys\ShopBundle\Model\Product\ProductData
         */
        public function createFromProduct(Product $product)
        {
            // ...
            $productData->code = $product->getCode();
        }
    }
    ```

4. Next, to add the `code` field to the form in administration edit [`\Shopsys\ShopBundle\Form\Admin\Product\ProductFormType`](../../src/Shopsys/ShopBundle/Form/Admin/Product/ProductFormType.php):
    ```php
    class ProductFormType 
    {
        /**
         * @param \Symfony\Component\Form\FormBuilderInterface $builder
         * @param array $options
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            // ...
            
            $builder->add('code', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length(['max' => 30]),
                ],
            ]);
        }
        
    }
    ```

5. Finally, you need to render the new field in the form template [`src/Shopsys/ShopBundle/Resources/views/Admin/Content/Product/detail.html.twig`](../../src/Shopsys/ShopBundle/Resources/views/Admin/Content/Product/detail.html.twig):
    ```twig
        ...
        {% block product_partno %}
            {{ form_row(form.productData.partno, { label: 'Part Number'|trans }) }}
        {% endblock %}
        
        {{ form_row(form.productData.code, { label: 'Code'|trans }) }}
        
        {% block product_ean %}
            {{ form_row(form.productData.ean, { label: 'EAN'|trans }) }}
        {% endblock %}
        ...
    ```
    otherwise it will be automatically rendered at the end of the form.
