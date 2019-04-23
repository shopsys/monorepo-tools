# Model Rules
In this article you will learn about model rules, what is and what is not model, and how to correctly create and insert new functionality into existing directory architecture.

## Hierarchy nesting
All classes related to the model are nested in the `Model` namespace inside the bundle namespace.
In your e-commerce project based on Shopsys Framework, you should put your classes inside `Shopsys\ShopBundle\Model\<MODEL>` (this namespace will be used below).
Core model classes of SSFW can be found in `Shopsys\FrameworkBundle\Model\<MODEL>`.

They can also be nested into deeper directory such as `Shopsys\ShopBundle\Model\Product\Search` if it encapsulate group of classes representing some specific functionality, for example search functionality for a `Product` entity.

## Rules
- Classes inside the model are grouped into model namespaces.
For example, if you want to add a new functionality that works with product, your class should be created in `\Shopsys\ShopBundle\Model\Product`.
- Main parts of a model such as `Facade` or `Repository` are grouped by a model they are responsible for, not by their type.
Eg. classes `Product`, `ProductRepository` and `ProductFacade` should all be inside the `Shopsys\ShopBundle\Model\Product` namespace together.
- All exceptions in a model should be in in a `Shopsys\ShopBundle\<MODEL>\Exception` namespace and they should implement a common interface using the [Marker Interface Pattern](https://en.wikipedia.org/wiki/Marker_interface_pattern), eg. [ProductException](/packages/framework/src/Model/Product/Exception/ProductException.php).
- All DQL and SQL operations related to a model should be in a model repository.
- Integration code is not a part of the model.
For example, forms or controllers should be outside the `Model` namespace.
- A model can be dependent on a component but not vice versa, this rule comes from definition of a [Component](../introduction/components.md).

## What is and what is not a model
*Model is a system of abstractions that describes selected aspect of a domain.*

That means that everything in a model should be related to some functionality of the domain, in our case, e-commerce.
An exception to this is integration code such as controllers or forms which are not a part of the model.

If you are creating new functionality that could be used, for example, in a portfolio application, like a navigation panel, you should create it as a component.
You can read more about components in [Components](../introduction/components.md).

Everything else that is related to our domain should be created into corresponding model namespace or create a new model namespace.

## Exceptions to rules
Some concepts in our current model do not follow the rules listed above.

### Models that will be moved
* `AdminNavigation` - will be moved to components
* `AdvancedSearch` - will be moved to components
* `Breadcrumb` - will be moved to components
* `ContactForm` - will be moved to components
* `Cookies` - will be moved under the `Article` model since it is closely related to articles
* `Grids` - grids are located in `Shopsys\FrameworkBundle\<MODEL>\Grid` namespaces, these will be moved to a separate `Shopsys\FrameworkBundle\Grid` namespace (similarly to forms)
* `Mail` - will be moved to components
* `Module` - will be moved to components
* `Sitemap` - will be moved to components
* `Localization` - will be moved to components
* `LegalConditions` - will be moved under the `Article` model since it is closely related to articles
* `Seo` - will be moved to components
* `Slider` - will be moved to components
* `Statistics` - will be moved to components

### Model without persisted entity representation
Some models do not have a persisted entity that represents a model.
For example one of them is `Feed`, even though it does not have a entity, it is related to the e-commerce domain and because of that we keep it in the model namespace.

Models without a persisted entity:
* `Feed`
* `Heureka`
* `ShopInfo`

## MultidomainEntityClassProvider
This provider serves for the framework to know which entities are [domain entities](entities.md#domain-entity).
Since this class is closely related to the model, it is placed there.
