# Entity Extension

This article describes the internals of entity extension system implemented in Shopsys Framework along with a quick guide on how to use it.
The entity extension is a work in progress.

You can read about alternative solutions we considered and the reasons behind this approach in [Entity Extension vs. Entity Generation](entity-extension-vs-entity-generation.md).

Let's suppose that we are implementing Dream project as a clone of the [project-base repository](https://github.com/shopsys/project-base).
Dream project is dependent on the glass-box [framework repository](https://github.com/shopsys/framework).
The framework is, of course, independent of our Dream project.

Entities in the framework are full-featured themselves and we want to extend them with our custom properties and associations.
We achieve this via inheritance:

![Dream project entities extend Framework entities](img/entity-extension.png)

Dream project in the example above has extended entities Category and Product.
An association with a custom entity Dream Product Speciality was added to Product.
CategoryDomain was not extended, this means that the extended Dream Category has an association with the original CategoryDomain.

Doctrine allows us to have only the original entity or the extended entity in the whole system, both are not possible.
So all associations, new objects, repositories, query builders - everything has to be consistent, otherwise, Doctrine will fail.

## How does it work?

The solution is based on Doctrine event subscribers and metadata manipulation.

It is important that the **EntityExtensionParentMetadataCleanerEventSubscriber** runs first and the **LoadORMMetadataSubscriber** runs last.
Otherwise, a conflict with other subscribers modifying the metadata would occur.

Correct order of relevant Doctrine event subscribers:
* EntityExtensionParentMetadataCleanerEventSubscriber
* Gedmo subscribers (*from [gedmo/doctrine-extensions](https://github.com/gedmo/doctrine-extensions)*)
* TranslatableListener (*from [prezent/doctrine-translatable](https://github.com/prezent/doctrine-translatable)*)
* LoadORMMetadataSubscriber (*from [joschi127/doctrine-entity-override-bundle](https://github.com/joschi127/doctrine-entity-override-bundle)*)

`EntityManagerDecorator` is then responsible for using the extended entities instead of their parents in EntityManager, Repositories and QueryBuilders.

### EntityExtensionParentMetadataCleanerEventSubscriber

LoadORMMetadataSubscriber (which must be executed as last) turns the parent entities into MappedSuperclass.
It is better for the parent entities to be turned into MappedSuperclass before any other metadata manipulation is done.
Along with this, it strips all metadata from the parent entities.
This is basically to avoid other event subscribers to consider the parent entities to be real hydratable entities.
The only real problem we encountered was Gedmo's TreeListener, that is used for nested trees of Category entities.

This event subscriber also clears metadata about inheritance from parent entities because,
in Doctrine, a MappedSuperclass entity cannot be also a root entity of true mapped inheritance.
The only real instance of true mapped inheritance in the framework is the OrderItem.

### LoadORMMetadataSubscriber

This is the subscriber that extends entities.
It turns the parent entities into MappedSuperclass and adds parents' metadata into the extended entities.
Also, it replaces all associations with parent entities by the extended entities.
It must have low priority so it runs after Gedmo and Prezent extensions.
Gedmo and Prezent add their own mapping, entity extension must be performed after all metadata are known.

### EntityManagerDecorator

The original `EntityManager` is decorated to use `EntityNameResolver` to resolve extended entities in relevant methods.
Using inheritance for this purpose was specifically discouraged in the original class annotation.
Decoration of this class requires to use `EntitiyManagerInterface` as a type-hint instead of just `EntityManager` across the whole application.

It is also responsible for instantiation of QueryBuilders and Repositories, which use the decorated EntityManager.

### EntityExtension\QueryBuilder

The original `QueryBuilder` is extended to use `EntityNameResolver` while adding new DQL parts.
The overridden method `add()` is used in all other relevant methods, such as `select()`, `from()`, `where()` etc.

### EntityNameResolver

The sole responsibility of this class is to resolve extended entity names in any variable that is provided using entity extension map.
It replaces the parent entity name by the extended entity name in strings, arrays and object properties (even private ones using reflection).

The various capabilities of this resolver are best described in its unit test `\Tests\FrameworkBundle\Unit\Component\EntityExtension\EntityNameResolverTest`.

### Factories

Entities are created by factories. If any part of framework creates an entity, it uses a factory.
So in the project, we can change the factory to produce extended entities instead of original and the whole system will create extended entities.
We enforce using factories by our coding standard sniff [`ObjectIsCreatedByFactorySniff`](../../packages/coding-standards/src/Sniffs/ObjectIsCreatedByFactorySniff.php).

Only exception are `*Translation` entities.
They are created by their owner entity.
If it is needed to extend the translation, it is also necessary to extend the owner entity and override the `createTranslation` method to produce the extended translation.

### Data and DataFactories

Entity data are extended by inheritance.
Since they are not persisted, there is no need to do anything like in case of entities.

Entity data are created by factories only.
These factories work as same as entity factories.
If any part of framework creates an entity data, it uses a factory.
So in the project, we can change the factory to produce extended entity data instead of original and the whole system will create extended entity data.

## OrderItem and true mapped inheritance

![class inheritance of the OrderItem entity](img/order-item.png)

If we want to extend OrderItem entity itself, we have to extend OrderItem and also all descendants and we end up with inheritance tree shown above.
In Dream project, the descendants (DreamOrderPayment etc.) must extend DreamOrderItem (a direct descendant of OrderItem),
so they have to contain duplicated code from the original descendant entities.
DiscriminatorMap must always contain descendants' FQN because LoadORMMetadataSubscriber reads raw original annotations.

## How can I extend an entity?

* Create a new entity in your `src/Shopsys/ShopBundle/Model` directory that extends already existing framework entity
  * keep entity and table annotations
  * you can add new properties and use annotations to configure ORM
* Add information about the entity extension into the container configuration
  * add it to the configuration parameter `shopsys.entity_extension.map`
  * use the parent entity name as a key and the extended entity name as a value
  * eg. `Shopsys\FrameworkBunde\Model\Product\Product: DreamProject\Model\Product\Product`
* Create a factory for this entity
  * Implement the factory interface from the framework
    * eg. `class ProductFactory implements ProductFactoryInterface`
  * Rewrite symfony configuration for the interface to alias your factory
    * eg.
      ```php
        Shopsys\FrameworkBundle\Model\Product\ProductFactoryInterface:
          alias: DreamProject\Model\Product\ProductFactory
      ```
* Create a new data object in your `src/Shopsys/ShopBundle/Model` directory that extends already existing framework entity data
* Create a factory for this entity data
* Implement the factory interface from the framework
    * eg. `class ProductDataFactory implements ProductDataFactoryInterface`
  * Rewrite symfony configuration for the interface to alias your factory
    * eg.
      ```php
        Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface:
          alias: DreamProject\Model\Product\ProductDataFactory
      ```
* Now your extended entity is automatically used instead of the parent entity:
  * in hydrated Doctrine references
  * in the EntityManager, Repositories and QueryBuilders
  * in newly created entities

*Tip: to see how it works in practice check out `\Tests\ShopBundle\Database\EntityExtension\EntityExtensionTest` that tests end-to-end extensibility of `Product`, `Category` and `OrderItem`.*
