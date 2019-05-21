# Introduction to Model Architecture

In this article you will learn about the model, its dependencies, [entities](#entity), [facades](#facade), [repositories](#repository) and their mutual relations.

## Definition of a model
The definition of a model is adopted from [Domain Driven Design (DDD)](https://stackoverflow.com/questions/1222392/can-someone-explain-domain-driven-design-ddd-in-plain-english-please/1222488#1222488). Model is a system of abstractions that describes selected aspect of a domain.

Domain is a sphere of knowledge or activity we build application logic around.
The domain of Shopsys Framework is e-commerce.

*Note: In Shopsys Framework, we also use the term domain for another concept which is an instance of eshop data accessible through an individual url address.
You can read more about this meaning of a domain in [Domain, Multidomain, Multilanguage](../introduction/domain-multidomain-multilanguage.md).*

Each domain has its logic which is the higher level rules for how objects of the domain model interact with one another.

Domain model of Shopsys Framework is located in [`Shopsys\FrameworkBundle\Model`](https://github.com/shopsys/framework/tree/master/src/Model). Its concept is to separate behavior and properties of objects from its persistence. This separation is suitable for code reusability, easier testing and it fulfills the Single Responsibility Principle.

Code belonging to the same feature is grouped together (eg. `Cart` and `CartItem`). Names of classes and methods are based on real world vocabulary to be more intuitive (eg. `OrderHashGenerator` or `getSellableProductsInCategory()`).

Model is mostly divided into three parts: Entity, Repository and Facade.

![model architecture schema](./img/model-architecture.png 'model architecture schema')

## Entity
Entity is a class encapsulating data. All entities are persisted by Doctrine ORM. One entity class usually represents one table in the database and one instance of the entity represents one row in the table. The entity is composed of fields, which can be mapped to columns in the table. Doctrine ORM annotations are used to define the details about the database mapping (types of columns, relations, etc.).

Entities are inspired by Rich Domain Model. That means entity is the place where domain logic belongs (e.g. `Product::changeVat()` sets vat and marks product for price recalculation).

Entities can be used by all layers of the model and even outside of model (eg. controller or templates).

You'll find more about our entities specialities in a [detailed article](entities.md).

### Example
```php
// FrameworkBundle/Model/Product/Product.php

namespace Shopsys\FrameworkBundle\Model\Product;

use Doctrine\ORM\Mapping as ORM;

// ...

/**
 * @ORM\Table(
 *     name="products"
 * )
 * @ORM\Entity
 */
class Product
{

    // ...

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $vat;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     */
    public function changeVat(Vat $vat)
    {
        $this->vat = $vat;
        $this->recalculatePrice = true;
    }

    // ...

}
```

## Repository
Is a class used to provide access to all entities of its scope. Repository enables code reuse of retrieving logic. Thanks to repositories, there is no need to use DQL/SQL in controllers or facades.

Repository methods have easily readable names and clear return types so IDE auto-completion works great.

In Shopsys Framework repository is mostly used to retrieve entities from the database using Doctrine but can be used to access any other data storage.

Repositories should be used only by facade so you should avoid using them in any other part of the application.

### Example
```php
// FrameworkBundle/Model/Cart/CartRepository.php

namespace Shopsys\FrameworkBundle\Model\Cart;

// ...

class CartRepository
{
    // ...

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getCartRepository()
    {
        return $this->em->getRepository(Cart::class);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     * @return \Shopsys\FrameworkBundle\Model\Cart\Cart|null
     */
    public function findByCustomerIdentifier(CustomerIdentifier $customerIdentifier)
    {
        $criteria = [];
        if ($customerIdentifier->getUser() !== null) {
            $criteria['user'] = $customerIdentifier->getUser()->getId();
        } else {
            $criteria['cartIdentifier'] = $customerIdentifier->getCartIdentifier();
        }

        return $this->getCartRepository()->findOneBy($criteria, ['id' => 'desc']);
    }

    /**
     * @param int $daysLimit
     */
    public function deleteOldCartsForUnregisteredCustomers($daysLimit)
    {
        $nativeQuery = $this->em->createNativeQuery(
            'DELETE FROM cart_items WHERE cart_id IN (
                SELECT C.id
                FROM carts C
                WHERE C.modified_at <= :timeLimit AND user_id IS NULL)',
            new ResultSetMapping()
        );

        $nativeQuery->execute([
            'timeLimit' => new DateTime('-' . $daysLimit . ' days'),
        ]);

        $nativeQuery = $this->em->createNativeQuery(
            'DELETE FROM carts WHERE modified_at <= :timeLimit AND user_id IS NULL',
            new ResultSetMapping()
        );

        $nativeQuery->execute([
            'timeLimit' => new DateTime('-' . $daysLimit . ' days'),
        ]);
    }

    // ...
}
```
*Note: Repositories in Shopsys Framework wrap Doctrine repositories. This is done in order to provide only useful methods with understandable names instead of generic API of Doctrine repositories.*

## Facade
Facades are a single entry-point into the model. That means you can use the same method in your controller, CLI command, REST API, etc. with the same results. All methods in facade should have single responsibility without any complex logic. Every method has a single use case and does not contain any business logic only sequence of calls of entities and repositories methods.

Facades as entry-point of the model can be used anywhere outside of the model.

Facades represent all available use-cases for specific model.

### Example
```php
// FrameworkBundle/Model/Cart/CartFacade.php

namespace Shopsys\FrameworkBundle\Model\Cart;

// ...

class CartFacade
{

    // ...

    /**
     * @param int $productId
     * @param int $quantity
     * @return \Shopsys\FrameworkBundle\Model\Cart\AddProductResult
     */
    public function addProductToCart($productId, $quantity)
    {
        $product = $this->productRepository->getSellableById(
            $productId,
            $this->domain->getId(),
            $this->currentCustomer->getPricingGroup()
        );
        $cart = $this->getCartOfCurrentCustomerCreateIfNotExists();

        /* @var $result \Shopsys\FrameworkBundle\Model\Cart\AddProductResult */
        $result = $cart->addProduct($product, $quantity, $this->productPriceCalculation, $this->cartItemFactory);

        $this->em->persist($result->getCartItem());
        $this->em->flush();

        return $result;
    }

    // ...

}
```

## Cooperation of layers
The controller handles the request (eg. [saved data](entities.md#entity-data) from form) and passes data to the facade.
The facade receives data from the controller and requests appropriate entities from the repository.
Entities and supporting classes (like recalculators, schedulers) processes data and returns output to the facade, that persist it by entity manager.

## Model extension
Entity extension is described in [Entity Extension article](../extensibility/entity-extension.md).

Other parts of a model can be extended by class inheritance and adding an alias to your `services.yml`, eg.:
```
services:
    Shopsys\FrameworkBundle\Model\Article\ArticleFacade: '@Shopsys\ShopBundle\Model\Article\ArticleFacade'
```

### Extending from a bundle
You can extend some entities from other bundles using [CRUD extension](https://github.com/shopsys/plugin-interface#crud-extension).
Other parts cannot be extended because PHP does not support multiple class inheritance.

## Model Rules
There are some model-specific rules that help up maintain easier usage of the model.
They also help which classes should be a part of the `Model` namespace and which shouldn't.
You can read more about them in [Model Rules](./model-rules.md).

## Read Model
Next to the standard model described in this article, there is also an extra layer called "read model" that separates templates and the model itself in particular reading use-cases.
You can read more in [Introduction to Read Model](/docs/model/introduction-to-read-model.md).
