# Custom Entities

When you need to add functionality that is not in the system, like an advertising campaign,
then you need your own, custom entities.

* The system is prepared and configured for custom entities.
The configuration is placed in [doctrine.yml](/project-base/app/config/packages/doctrine.yml), section `doctrine.orm.mappings`.
* An entity should be in namespace `Shopsys\ShopBundle\Model` (directory `src/Shopsys/ShopBundle/Model`).
* We use annotations for Doctrine mapping.
More in [annotations reference](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/annotations-reference.html).
* And maybe you are also interested in [entity extension](/docs/extensibility/entity-extension.md).

## Cookbook

For step by step instructions on how to add a new entity to your project, follow ["Adding a new entity" cookbook](/docs/cookbook/adding-a-new-entity.md).
