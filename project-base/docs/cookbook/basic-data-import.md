# Basic Data Import
This cookbook will guide you through the process of importing data into your e-shop from an external source,
e.g. from an information system.

After completing this cookbook you should know:
- how to implement basic data import
- what are the best practices for importing data
- what are the pitfalls of importing data and how can you deal with them

## Example: Products import step by step
Let us create simple products import from an external source.
The source is mocked with [apiari.io service](http://docs.ssfwbasicdataimportdemo.apiary.io/) which returns example products data in JSON format:
```json
[
    {
        "id": 1001,
        "name": "Emos LED light",
        "price_without_vat": "354",
        "vat_percent": "21",
        "ean": "12345678901",
        "brand_id": 101,
        "description": "light source: 1 x 3W CREE LED + 12 LED 5 mm ...",
        "stock_quantity": 25
    },
    {
        "id": 1002,
        "name": "Digital camera EasyPix GoXtreme Impact, Full HD Action 1080p, white",
        "price_without_vat": "2050",
        "vat_percent": "21",
        "ean": "12345678902",
        "brand_id": 102,
        "description": "EasyPix GoXtreme - outdoor waterproof camera.",
        "stock_quantity": 18
    },
    {
        "id": 1004,
        "name": "Power gril PARTY TIME Tescoma",
        "price_without_vat": "2478.51",
        "vat_percent": "21",
        "ean": "12345678904",
        "brand_id": 103,
        "description": "Highly powerful mobile grill with turbo vent.",
        "stock_quantity": 3
    }
]
```

### Step 1 - Add `$externalId` to [Product](../../../packages/framework/src/Model/Product/Product.php) [entity](../introduction/basics-about-model-architecture.md#entity)
We need to store the relation between your application database and the external source of data because later, in data transfer processing,
we will be deciding whether to create a new product or update existing one, based on the `$externalId` attribute.
If you do not know how to add an attribute to an entity, take a look at [the cookbook](adding-new-attribute-to-an-entity.md).
It is not necessary to edit the `$externalId` attribute in administration, so you do not need to modify any forms (i.e. you can omit last two steps of [the cookbook](adding-new-attribute-to-an-entity.md)).

### Step 2 - Create new cron module
Cron modules are the best way to handle data downloaded from external sources
because they can be scheduled, run on background and even iterated when necessary.

#### 2.1 - Add new `ImportProductsCronModule` class that implements [`SimpleCronModuleInterface`](../../../packages/plugin-interface/src/Cron/SimpleCronModuleInterface.php)
```php
// FrameworkBundle/Component/Cron/SimpleCronModuleInterface.php

namespace Shopsys\FrameworkBundle\Model\Product;

use Shopsys\FrameworkBundle\Component\Cron\SimpleCronModuleInterface;
use Symfony\Bridge\Monolog\Logger;

class ImportProductsCronModule implements SimpleCronModuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function setLogger(Logger $logger)
    {
    }

    public function run()
    {
    }
}
```

*Note: Cron modules are not suitable for data transfers initialized by an external source,
you should implement Web Services for that purpose.*

#### 2.2 - Register new cron module in [`services/cron.yml`](../../../packages/framework/src/Resources/config/services/cron.yml)
```yaml
# FrameworkBundle/Resources/config/services/cron.yml

Shopsys\FrameworkBundle\Model\Product\ImportProductsCronModule:
      class: Shopsys\FrameworkBundle\Model\Product\ImportProductsCronModule
      tags:
        - { name: shopsys.cron, hours: '*/3', minutes: '0' }
```

*Note: You can schedule cron modules to run at any time you want using [cron expression](https://en.wikipedia.org/wiki/Cron#CRON_expression). This example module will be run every 3 hours.*

### Step 3 - Process incoming data in our new `ImportProductsCronModule`
In this step, we will download data from the external source
and then decide whether to create or update existing product based on `$externalId`.

#### 3.1 - Download external data and create or update product based on `$externalId`
If a product exists with given `$externalId` we modify it, otherwise, we need to create a new one.
```php
// FrameworkBundle/Model/Product/ImportProductsCronModule.php

// ...

use Shopsys\FrameworkBundle\Model\Product\ProductFacade;

// ...

const PRODUCT_DATA_URL = 'http://private-53864-productsimportexample.apiary-mock.com/products';

// ...

/**
 * @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade
 */
private $productFacade;

// ...

public function __construct(
    ProductFacade $productFacade
) {
    $this->productFacade = $productFacade;
}

public function run()
{
    $externalProductsJsonData = file_get_contents(self::PRODUCT_DATA_URL);
    $externalProductsData = json_decode($externalProductsJsonData, true);
    $this->importExternalProductsData($externalProductsData);
}

/**
 * @param array $externalProductsData
 */
private function importExternalProductsData(array $externalProductsData)
{
    foreach ($externalProductsData as $externalProductData) {
        $externalId = $externalProductData['id'];

        $product = $this->productFacade->findByExternalId($externalId); // will be implemented in next steps
        if ($product === null) {
           $this->createProduct($externalProductData); // will be implemented in next steps
        } else {
           $this->editProduct($product, $externalProductData); // will be implemented in next steps
        }
    }
}
```

*Note: We need to know whether the product with given `$externalId` exists.
For that purpose, we use [`ProductFacade`](../../../packages/framework/src/Model/Product/ProductFacade.php) ([more about facades](../introduction/basics-about-model-architecture.md#facade))
which uses [`ProductRepository`](../../../packages/framework/src/Model/Product/ProductRepository.php) ([more about repositories](../introduction/basics-about-model-architecture.md#repository))
that can talk to the persistence layer. So we will implement new methods in these classes in next two steps.*

#### 3.2 - Implement method `findByExternalId()` in [`ProductRepository`](../../../packages/framework/src/Model/Product/ProductRepository.php) in order to be able find a [`Product`](../../../packages/framework/src/Model/Product/Product.php) by an external ID
```php
// FrameworkBundle/Model/Product/ProductRepository.php

// ...

/**
 * @param int $externalId
 * @return \Shopsys\FrameworkBundle\Model\Product\Product|null
 */
public function findByExternalId($externalId)
{
   return $this->getProductRepository()->findOneBy(['externalId' => $externalId]);
}
```

#### 3.3 - Implement method `findByExternalId()` in [`ProductFacade`](../../../packages/framework/src/Model/Product/ProductFacade.php) in order to get [`Product`](../../../packages/framework/src/Model/Product/Product.php) from repository

```php
// FrameworkBundle/Model/Product/ProductFacade.php

// ...

/**
 * @param int $externalId
 * @return \Shopsys\FrameworkBundle\Model\Product\Product|null
 */
public function findByExternalId($externalId)
{
    return $this->productRepository->findByExternalId($externalId);
}
```

#### 3.4 - Implement `ImportProductsCronModule::createProduct()` and `ImportProductsCronModule::updateProduct()`
As an entry-point for data processing in Shopsys Framework, we use facades.
In this case, [`ProductFacade`](../../../packages/framework/src/Model/Product/ProductFacade.php)
and its methods `create()` and `edit()`.
Those methods expect [`ProductEditData`](../../../packages/framework/src/Model/Product/ProductEditData.php)
class as a parameter, you can use [`ProductEditDataFactory`](../../../packages/framework/src/Model/Product/ProductEditDataFactory.php) to create it.

```php
// FrameworkBundle/Model/Product/ImportProductsCronModule.php

// ...

use Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory;

// ...

/**
 * @var \Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory
 */
private $productEditDataFactory;

// ...

public function __construct(
    // ...

    ProductEditDataFactory $productEditDataFactory
) {
    // ...
    $this->productEditDataFactory = $productEditDataFactory;
}

// ...

/**
 * @param array $externalProductData
 */
private function createProduct(array $externalProductData) {
    $productEditData = $this->productEditDataFactory->createDefault();
    $this->fillProductEditData($productEditData, $externalProductData); // will be implemented in next step

    $this->productFacade->create($productEditData);
}

/**
 * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
 * @param array $externalProductData
 */
private function editProduct(Product $product, array $externalProductData) {
    $productEditData = $this->productEditDataFactory->createFromProduct($product);
    $this->fillProductEditData($productEditData, $externalProductData); // will be implemented in next step

    $this->productFacade->edit($product->getId(), $productEditData);
}
```

#### 3.5 - Implement `ImportProductsCronModule::fillProductEditData()`
Finally, we can implement the private method for filling data object
[`ProductEditData`](../../../packages/framework/src/Model/Product/ProductEditData.php) with external source data.
```php
// FrameworkBundle/Model/Product/ImportProductsCronModule.php

// ...

use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductEditData;

// ...

const DOMAIN_ID = 1;
const LOCALE = 'en';

// ...

/**
 * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade
 */
private $vatFacade;

// ...

public function __construct(
    // ...

    VatFacade $vatFacade
) {
    $this->vatFacade = $vatFacade;
}


/**
 * @param \Shopsys\FrameworkBundle\Model\Product\ProductEditData $productEditData
 * @param array $externalProductData
 */
private function fillProductEditData(ProductEditData $productEditData, array $externalProductData)
{
    $productEditData->productData->name[self::LOCALE] = $externalProductData['id'];
    $productEditData->productData->price = $externalProductData['price_without_vat'];
    $productEditData->productData->vat = $this->vatFacade->getVatByPercent($externalProductData['vat_percent']); // will be implemented in next step
    $productEditData->productData->ean = $externalProductData['ean'];
    $productEditData->descriptions[self::DOMAIN_ID] = $externalProductData['description'];
    $productEditData->productData->usingStock = true;
    $productEditData->productData->stockQuantity = $externalProductData['stock_quantity'];
}
```
*Note: In order to be able to use stock quantity, we must enable it by setting the `$usingStock` attribute to `true`.*

*Note 2: Data from external source contain only integer value for vat percent information but we need
[`Vat`](../../../packages/framework/src/Model/Pricing/Vat/Vat.php) object
in [`ProductData`](../../../packages/framework/src/Model/Product/ProductData.php).
So we will implement appropriate methods
in [`VatRepository`](../../../packages/framework/src/Model/Pricing/Vat/VatRepository.php)
and in [`VatFacade`](../../../packages/framework/src/Model/Pricing/Vat/VatRepository.php) in next steps.*

#### 3.6 - Implement method `getVatByPercent()` in [`VatRepository`](../../../packages/framework/src/Model/Pricing/Vat/VatRepository.php) in order to load [`Vat`](../../../packages/framework/src/Model/Pricing/Vat/Vat.php) by percent
```php
// FrameworkBundle/Model/Pricing/Vat/VatRepository.php

namespace Shopsys\Model\Pricing\Vat;

class VatRepository
{
    // ...

    /**
     * @param int $percent
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     */
    public function getVatByPercent($percent)
    {
        $vat = $this->getVatRepository()->findOneBy(['percent' => $percent]);

        if ($vat === null) {
            throw new \Shopsys\FrameworkBundle\Model\Pricing\Vat\Exception\VatNotFoundException('Vat with ' . $percent . '% not found.');
        }

        return $vat;
    }

}
```

**Warning: The method throws an exception when [`Vat`](../../../packages/framework/src/Model/Pricing/Vat/Vat.php) object is not found by given percent value.
Do not forget to handle it (e.g. skip the product data processing and log the exception).**

#### 3.7 - Implement method `getVatByPercent()` in [`VatFacade`](../../../packages/framework/src/Model/Pricing/Vat/VatFacade.php)
```php
// FrameworkBundle/Model/Pricing/Vat/VatFacade.php

namespace Shopsys\Model\Pricing\Vat;

class VatFacade
{
    // ...

    /**
     * @param int $percent
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     */
    public function getVatByPercent($percent)
    {
        return $this->vatRepository->getVatByPercent($percent);
    }

}
```

### Step 4 - Everything is done, you can run it now
#### 4.1 Run `phing cron-list` in console
As an output, you will get a list of all available cron modules.

![example output from "cron-list" phing target](img/cron-list-output-example.png 'example output from "cron-list" phing target')

#### 4.2 - Find your module and run appropriate console command
`php bin/console shopsys:cron --module="shopsys\frameworkbundle\model\product\importproductscronmodule"`

## Best practices
- Validate all incoming data before putting them into data objects.
    - You will avoid SQL errors (e.g. when incoming text is longer than database column allows).
- Transfer modifications only (if possible).
    - Persist the last successful transfer start datetime so next time you can import changes done afterward.
    - It means less data to process.
- Transfer deleted entities too.
    - It is often omitted, but it is necessary if you want to keep consistency between your application database and the external source.
- Use database transactions.
    - Use `EntityManger` methods `beginTransaction()`, `commit()` and `rollback()`.
    - Do not forget to clear identity map when doing a rollback (so even entity modifications are reverted).
    - [`IteratedCronModuleInterface`](https://github.com/shopsys/plugin-interface/blob/master/src/Cron/IteratedCronModuleInterface.php) offers a way to implement longer processes that can span over many iterations.
- Disable entity manager SQL logging (use [`SqlLoggerFacade::temporarilyDisableLogging()`](../../../packages/framework/src/Component/Doctrine/SqlLoggerFacade.php)).
    - By default, every executed SQL query is logged and that slows down the process and consumes a lot of memory when there are many iterations.
    - Do not forget to re-enable the logging after the processing is done (use [`SqlLoggerFacade::reenableLogging()`](../../../packages/framework/src/Component/Doctrine/SqlLoggerFacade.php)).
- Logging of some key functions might be helpful for future debugging
but it is not a good idea to log "everything". Too many information in logs might be counterproductive.
- Clear entity manager identity map once in a while, because `EntityManager::flush()` searches
for changes in all mapped entities and after time it consumes a huge amount of resources to persist a single entity.
    - Call `EntityManager::flush()` with parameter (i.e. entity or array of entities you want to flush) anytime it is possible.
     **Warning: Flushing is not cascade operation, i.e. when you flush entity that contains any other entities (e.g. translations),
    these are not flushed automatically. You should not forget to flush them as well.**
    - Use [`EntityManagerFacade::clear()`](../../../packages/framework/src/Component/Doctrine/EntityManagerFacade.php)
    instead of `EntityManager::clear()` for clearing identity map as it clears application cache as well.
    - You should load any entity again after clearing identity map because any attempt to flush the old one will result in an exception.
- Use streamed input for XML and JSON.
    - So you do not load huge files at once (can lead to memory overflow).
- Store external source credentials in `app/config/parameters.yml`.
    - Storing credentials in local configuration instead of hard-coding them in source code prevents from accidental corrupting of production data.
- Restrict editing of the transferred fields in administration.
    - At least, mark them as transferred to avoid confusion when an administrator changes the field value and then data import overrides the value.
- Transfer overview in administration can be very useful for both administrator and developer of an e-shop.
    - It is handy to know which transfers are currently in progress, which are scheduled, which failed etc.
- Be careful with the order of your data transfers.
    - For example, products have an association with their categories,
    so first you want to transfer products and then their relation to categories.

## Pitfalls
- External data source often sends null values as empty strings, so be very careful with validating the incoming data. (`$value !== null` might not be sufficient in that case).
- When processing a large amount of data, you can use native queries instead of using ORM, which might save you a certain amount of SQL queries.
On the other hand, you have to handle all related logic manually then.
- It is necessary to mark transferred products for recalculations (e.g. price, visibility, ...).
- In the current state, e-shop can not handle transferring huge amount (thousands) of categories.
    - Categories use "nested set" structure (`@Gedmo\Tree(type="nested")`) which produces many SQL queries.

## Conclusion

Now you know how to implement simple data transfer to your e-shop from an external source.
You know why you should persist external ID and how to decide whether create new entities or update existing ones.
You learned about Shopsys Framework cron modules, how to create and run them. You also know how to get desired objects based on external data
(e.g. [`Vat`](../../../packages/framework/src/Model/Pricing/Vat/Vat.php) object based on vat percent).
You are familiar with best practices for implementing data transfers,
what pitfalls you can encounter with
and what are the ways of dealing with them.

