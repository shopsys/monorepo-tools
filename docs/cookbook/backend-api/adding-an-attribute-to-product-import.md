# Adding an Attribute to Product Import

This short cookbook describes the steps that you need to do when you want to add a new attribute to the product import methods of the [backend API](/docs/backend-api/introduction-to-backend-api.md).

Let's say you have added `extId` attribute to `Product` entity following [Adding New Attribute to an Entity cookbook](/docs/cookbook/adding-new-attribute-to-an-entity.md) and you want to include the attribute in the import as well.

## 1. Extend `ProductDataFactory` for the API
[`ProductDataFactory`](/packages/backend-api/src/Controller/V1/Product/ProductDataFactory.php) is responsible for converting request body to [`ProductData`](/project-base/src/Shopsys/ShopBundle/Model/Product/ProductData.php).
You have to add attributes to `ProductData` object during the conversion.

```php
namespace Shopsys\ShopBundle\Controller\Api\V1\Product;

use Shopsys\BackendApiBundle\Controller\V1\Product\ProductDataFactory as BaseProductDataFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductData;

class ProductDataFactory extends BaseProductDataFactory
{
    protected function setProductDataByApi(ProductData $productData, array $productApiData): void
    {
        parent::setProductDataByApi($productData, $productApiData);
        $this->apiDataSetter->setValueIfExists('extId', $productApiData, $productData);
    }
}
```

*Take a look into the [`ApiDataSetter`](/packages/backend-api/src/Component/DataSetter/ApiDataSetter.php), it contains useful method for conversion like `setDateTimeValueIfExists`, `setMultidomainValueIfExists` and `setMultilanguageValueIfExists`.*

## 2. Extend `ProductApiDataValidator`

[`ProductApiDataValidator`](/packages/backend-api/src/Controller/V1/Product/ProductApiDataValidator.php) is responsible for validating imported data.
If you need to add a field to the import, you have to always extend this validator.

*We use pure [Symfony Validations](https://symfony.com/doc/3.4/validation.html), se please read about them first.*

```php
namespace Shopsys\ShopBundle\Controller\Api\V1\Product;

use Shopsys\BackendApiBundle\Controller\V1\Product\ProductApiDataValidator as BaseProductApiDataValidator;
use Symfony\Component\Validator\Constraints;

class ProductApiDataValidator extends BaseProductApiDataValidator
{
    /**
     * @return array
     */
    protected function getConstraintDefinition(): array
    {
        $definition = parent::getConstraintDefinition();
        $definition['fields']['extId'] = new Constraints\Optional([
            new Constraints\Type([
                'type' => 'int',
                'message' => 'The value {{ value }} is not a valid {{ type }}.',
            ]),
            new Constraints\NotNull(),
        ]);

        return $definition;
    }
}
```

The validation definition is used for both creating products and updating products.
If you need different validation for creating and updating, feel free to implement your own [`ProductApiDataValidatorInterface`](/packages/backend-api/src/Controller/V1/Product/ProductApiDataValidatorInterface.php).

## 3. Add information about the class extensions into the container configuration in [`services.yml`](/project-base/src/Shopsys/ShopBundle/Resources/config/services.yml)
```yaml
Shopsys\BackendApiBundle\Controller\V1\Product\ProductDataFactoryInterface: '@Shopsys\ShopBundle\Controller\Api\V1\Product\ProductDataFactory'
Shopsys\BackendApiBundle\Controller\V1\Product\ProductApiDataValidatorInterface: '@Shopsys\ShopBundle\Controller\Api\V1\Product\ProductApiDataValidator'
```

## Conclusion
Now, the `extId` attribute is included in the product import.
You can import the attribute during creating new products by [POST](/docs/backend-api/api-methods.md#add-product) method or update by [PATCH](/docs/backend-api/api-methods.md#partial-product-update) method, just [try it](/docs/backend-api/introduction-to-backend-api.md#try-it) and see it for yourself.
