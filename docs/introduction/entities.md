# Entities

This article describes how we work with entities and our specialities.
1. Entity is a class encapsulating data and you can read more what is an entity in the [model architecture article](basics-about-model-architecture.md).
1. Entities are created by [factories](#entity-factory).
1. For domain-specific data we use [domain entities](#domain-entity).
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
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $quantity
     * @param string $watchedPrice
     * @return \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem
     */
    public function create(
        CustomerIdentifier $customerIdentifier,
        Product $product,
        int $quantity,
        string $watchedPrice
    ): CartItem;
}
```

The factory has an implementation in the framework and can be overwritten in your project when you need to work with an extended entity.
You can read about entity extension in a [separate article](../wip_glassbox/entity-extension.md).


## Domain entity
It is an entity which encapsulates data that are domain-specific (similarly to an Entity Translation encapsulating locale-specific data).
Domain entity has a bidirectional many-to-one association to its main entity.
That means that you can access domain entity through entity itself and vice versa.

Setting the properties of a domain entity is always done via the main entity itself.
Basically, that means only the main entity knows about the existence of domain entities.
The rest of the application uses the main entity as a proxy to the domain-specific properties.

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

## Entity data

Is a data object that is used to transfer data through application and also to create an entity.
The entity data can be created in controllers (or other data source), then propagated via facade and finally used to create the entity.
The entity data can be also created from an entity, and propagated to controllers or other parts of application.

Entity data have all attributes public and is mutable.
Entity data have a constructor without parameters and all parameters are initialized in the constructor.

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
     * @var string[]
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

## Entity data factory

Is the only place where entity data is created.
The framework must allow using extended entity data and this problem is solved, as same as with entities, by factories.
We enforce using factories by our coding standard sniff [`ObjectIsCreatedByFactorySniff`](../../packages/coding-standards/src/Sniffs/ObjectIsCreatedByFactorySniff.php).

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
