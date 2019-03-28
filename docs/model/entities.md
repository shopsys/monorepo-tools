# Entities

This article describes how we work with entities and our specialities.
1. Entity is a class encapsulating data and you can read more what is an entity in the [model architecture article](introduction-to-model-architecture.md).
1. Entities are created by [factories](#entity-factory).
1. For domain-specific data we use [domain entities](#domain-entity).
1. For language-specific data we use [translation entities](#translation-entity).
1. Data that we need for entity construction are encapsulated in [entity data](#entity-data).
1. Entity data are created by [entity data factories](#entity-data-factory).

## Entity factory

Is a class that creates an entity.
The framework must allow using extended entities and this problem is solved using factories.
We enforce using factories by our coding standard sniff [`ObjectIsCreatedByFactorySniff`](../../packages/coding-standards/src/Sniffs/ObjectIsCreatedByFactorySniff.php).

The only entities that are not created by a factory are `*Translation` and `*Domain` entities.
These entities are created by their main entity.

### Example
```php
// FrameworkBundle/Model/Cart/Item/CartItemFactoryInterface.php

namespace Shopsys\FrameworkBundle\Model\Cart\Item;

// ...

interface CartItemFactoryInterface
{

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Cart $cart
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $quantity
     * @param string $watchedPrice
     * @return \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem
     */
    public function create(
        Cart $cart,
        Product $product,
        int $quantity,
        string $watchedPrice
    ): CartItem;
}
```

The factory has an implementation in the framework and can be overwritten in your project when you need to work with an extended entity.
You can read about entity extension in a [separate article](../extensibility/entity-extension.md).


## Domain entity
It is an entity which encapsulates data that are domain-specific (similarly to an Entity Translation encapsulating locale-specific data).
Domain entity has a bidirectional many-to-one association to its main entity.
That means that you can access domain entity through entity itself and vice versa.

Setting the properties of a domain entity is always done via the main entity itself.
Basically, that means only the main entity knows about the existence of domain entities.
The rest of the application uses the main entity as a proxy to the domain-specific properties.

Sometimes you need to find all domain entities programmatically (eg. in [`CreateDomainsDataCommand`](/packages/framework/src/Command/CreateDomainsDataCommand.php)).
You can use the [`MultidomainEntityClassFinderFacade`](/packages/framework/src/Component/Domain/Multidomain/MultidomainEntityClassFinderFacade.php) which searches for all registered entities that have a composite identifier including a `domainId` field.
Exceptions (both for including and excluding particular class) can be provided via an implementation of [`MultidomainEntityClassProviderInterface`](/packages/framework/src/Component/Domain/Multidomain/MultidomainEntityClassProviderInterface.php).
You should provide your own implementation if you need to alter the list of domain entities (otherwise, [`MultidomainEntityClassProvider`](/packages/framework/src/Model/MultidomainEntityClassProvider.php) will be used).

### Example
```php
// FrameworkBundle/Model/Product/Brand/BrandDomain.php

namespace Shopsys\FrameworkBundle\Model\Product\Brand
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="brand_domains")
 * @ORM\Entity
 */
class BrandDomain
{
     /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\Brand
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Product\Brand\Brand", inversedBy="domains")
     * @ORM\JoinColumn(nullable=false, name="brand_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $brand;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $domainId;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $seoTitle;

    // ...

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @param int $domainId
     */
    public function __construct(Brand $brand, $domainId)
    {
        $this->brand = $brand;
        $this->domainId = $domainId;
    }

    /**
     * @return string|null
     */
    public function getSeoTitle()
    {
        return $this->seoTitle;
    }

    /**
     * @param string|null $seoTitle
     */
    public function setSeoTitle($seoTitle)
    {
        $this->seoTitle = $seoTitle;
    }

    // ...

}
```

...and its main entity `Brand` working as a proxy:

```php
// FrameworkBundle/Model/Product/Brand/Brand.php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

/**
 * @ORM\Table(name="brands")
 * @ORM\Entity
 */
class Brand extends AbstractTranslatableEntity
{

    // ...

    /**
     * @param int $domainId
     * @return string
     */
    public function getSeoTitle(int $domainId)
    {
        return $this->getBrandDomain($domainId)->getSeoTitle();
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain
     */
    protected function getBrandDomain(int $domainId)
    {
        foreach ($this->domains as $domain) {
            if ($domain->getDomainId() === $domainId) {
                return $domain;
            }
        }

        throw new BrandDomainNotFoundException($this->id, $domainId);
    }

    // ...

}
```

## Translation entity

We use [prezent/doctrine-translatable](https://github.com/Prezent/doctrine-translatable) for translated attributes.
The entity extends `\Shopsys\FrameworkBundle\Model\Localization\AbstractTranslatableEntity` as the `Brand` does in example below.

Setting the properties of a domain entity is always done via the main entity itself.
Basically, that means only the main entity knows about the existence of translation entities.
The rest of the application uses the main entity as a proxy to the translation-specific properties.
The extension creates instances of translated entities on-demand and this creation is transparent for user of domain entity.
*The concept is similar to [domain entities](#domain-entity)* but uses Doctrine extension.

### Example
```php
// FrameworkBundle/Model/Product/Brand/BrandTranslation.php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * @ORM\Table(name="brand_translations")
 * @ORM\Entity
 */
class BrandTranslation extends AbstractTranslation
{
    /**
     * @Prezent\Translatable(targetEntity="Shopsys\FrameworkBundle\Model\Product\Brand\Brand")
     */
    protected $translatable;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
```

...and its main entity `Brand` working as a proxy:

```php
// FrameworkBundle/Model/Product/Brand/Brand.php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

/**
 * @ORM\Table(name="brands")
 * @ORM\Entity
 */
class Brand extends AbstractTranslatableEntity
{

    // ...

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData $brandData
     */
    protected function setTranslations(BrandData $brandData)
    {
        foreach ($brandData->descriptions as $locale => $description) {
            $brandTranslation = $this->translation($locale);
            /* @var $brandTranslation \Shopsys\FrameworkBundle\Model\Product\Brand\BrandTranslation */
            $brandTranslation->setDescription($description);
        }
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandTranslation
     */
    protected function createTranslation()
    {
        return new BrandTranslation();
    }

    /**
     * @param string $locale
     * @return string
     */
    public function getDescription($locale = null)
    {
        return $this->translation($locale)->getDescription();
    }

    // ...

}
```

## Entity data

Is a data object that is used to transfer data through application and also to create an entity.
The entity data can be created in controllers (or other data source), then propagated via facade and finally used to create the entity.
The entity data can be also created from an entity, and propagated to controllers or other parts of application.

Entity data have all attributes public and is mutable.
Entity data have a constructor without parameters and all parameters are initialized in the constructor.
Entity data can contain methods for getting part of it's data
(eg. [`OrderData`](/packages/framework/src/Model/Order/OrderData.php), method `getNewItemsWithoutTransportAndPayment`).

### Example

```php
// FrameworkBundle\Model\Product\Brand\BrandData.php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

use Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData;
use Shopsys\FrameworkBundle\Form\UrlListData;

class BrandData
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var \Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData
     */
    public $image;

    /**
     * @var string[]|null[]
     */
    public $descriptions;

    /**
     * @var \Shopsys\FrameworkBundle\Form\UrlListData
     */
    public $urls;

    /**
     * @var string[]|null[]
     */
    public $seoTitles;

    /**
     * @var string[]|null[]
     */
    public $seoMetaDescriptions;

    /**
     * @var string[]|null[]
     */
    public $seoH1s;

    public function __construct()
    {
        $this->name = '';
        $this->image = new ImageUploadData();
        $this->descriptions = [];
        $this->urls = new UrlListData();
        $this->seoTitles = [];
        $this->seoMetaDescriptions = [];
        $this->seoH1s = [];
    }
}
```

### Recommendations for data object properties

#### Scalars

Scalars are the most typical properties in data objects.
You usually don't have a default value for a scalar field, so your PHPDoc annotation will usually look like `string|null`, `int|null`, `float|null`.

If you need to transfer boolean data, we recommend using `bool` with a default value in the constructor (`true`/`false`) because `false` and `null` behaves similarly in php.

#### Arrays

If you need to transfer arrays, use PHPDoc annotation `string[]`, `int[]`, `float[]`, `bool[]` and initialize an empty array in the constructor.

If you care about array keys, use a name of the key in the property name in form `propertyByKey`. Eg. [`TransportData::$pricesByCurrencyId`](/packages/framework/src/Model/Transport/TransportData.php).

#### Unknown types

We don't recommend to use `mixed` or `array` PHPDoc annotation as they aren't expressive.

The only exception in the framework is `$pluginData` in `CategoryData` and `ProductData`.
The PHPDoc annotation is an `array` in this case because plugins can contain any type.

#### Entities

It is common that you need to transfer an entity to form or other parts of the system.

If you need to transfer one entity, use PHPDoc annotation `entity|null`, eg. `\Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat|null` in [`TransportData::$vat`](/packages/framework/src/Model/Transport/TransportData.php).
If you need to transfer a collection of entities, use PHPDoc annotation `entity[]` and initialize the array in the constructor, eg. `\Shopsys\FrameworkBundle\Model\Payment\Payment[]` in [`TransportData::$payments`](/packages/framework/src/Model/Transport/TransportData.php).

#### Money

To transfer monetary values (*prices, account balances, discount amounts, price limits etc.*) you should always use `\Shopsys\FrameworkBundle\Component\Money\Money` (optionally nullable or as an array).
You may initialize a default value in the constructor or in the data factory (eg. with `Money::zero()`).

You can read more about the `Money` class in [How to Work with Money](/docs/model/how-to-work-with-money.md).

#### Images

To transfer images via the system, use PHPDoc annotation `\Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData` and initialize the field in the constructor as you can see in `BrandData` example above.

#### URL addresses

To transfer URL addresses via the system, use PHPDoc annotation `\Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\UrlListData` and initialize the field in the constructor as you can see in `BrandData` example above.

#### Multidomain

[Multidomain property](/docs/introduction/domain-multidomain-multilanguage.md#multidomain-attribute) is an array and has to be indexed by `domainId` - an integer ID of the given domain.
An example of such property is a `seoH1s` in the `BrandData` example above.
Data factory has to create an item in this array for each domain ID, otherwise domain entities would not be created correctly (a domain entity should exist for each domain, even with null values).

Therefore the multidomain field has PHPDoc annotation `string[]|null[]` or `int[]|null[]`.
For boolean multidomain properties, we recommend using default value filled in the factory and PHPDoc annotation `bool[]` only, eg. property [`TransportData::$enabled`](/packages/framework/src/Model/Transport/TransportData.php).

#### Multilanguage

[Multilanguage property](/docs/introduction/domain-multidomain-multilanguage.md#multilanguage-attribute) is an array and has to be indexed by `locale` - a string identifier of language (you can find them in [`domains.yml`](/project-base/app/config/domains.yml)).
An example of such property is a `descriptions` in the `BrandData` example above.
Data factory has to create an item in this array for each locale, otherwise translation entities would not be created correctly (a translation entity should exist for each locale, even with null values).
Therefore the multidomain field has PHPDoc annotation `string[]|null[]`.

#### Data objects

You can use even data object within your data object when you need composition.
Eg. [`CustomerData`](/packages/framework/src/Model/Customer/CustomerData.php) or [`OrderData`](/packages/framework/src/Model/Order/OrderData.php) (contains `OrderItemData`).

## Entity data factory

Is the only place where entity data is created.
The framework must allow using extended entity data and this problem is solved, as same as with entities, by factories.
We enforce using factories by our coding standard sniff [`ObjectIsCreatedByFactorySniff`](../../packages/coding-standards/src/Sniffs/ObjectIsCreatedByFactorySniff.php).

Data factory can also fill default values
(e.g. [`PaymentDataFactory`](/packages/framework/src/Model/Payment/PaymentDataFactory.php) fills default VAT for new payment objects).

### Example

```php
// FrameworkBundle/Model/Product/Brand/BrandDataFactoryInterface.php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

interface BrandDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData
     */
    public function create(): BrandData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand $brand
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData
     */
    public function createFromBrand(Brand $brand): BrandData;
}
```

The factory has an implementation in the framework and can be overriden in your project.

### Full example of entity construction

```php
$brandData = $this->brandDataFactory->create();
// $brandData->name = ...
// ...
$brand = $this->brandFactory->create($brandData);
```
