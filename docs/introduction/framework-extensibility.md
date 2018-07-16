# Framework Extensibility

This article summarizes the current possibilities of the framework extension,
provides a list of customizations that are not achievable now but are planned to be enabled soon, 
as well as a list of customizations that are not (and will not be) possible at all.

## What is achievable easily
* [Extending an entity](/docs/wip_glassbox/entity-extension.md)
    * [Adding a new attribute](/docs/cookbook/adding-new-attribute-to-an-entity.md)
* [Extending particular forms in the administration](/docs/wip_glassbox/form-extension.md) without the need of the template overriding
* [Customizing database migrations](/docs/introduction/database-migrations.md)
    * adding a new migration as well as skipping and reordering the existing ones
* Configuring the smoke tests (see [`RouteConfigCustomization`](/project-base/tests/ShopBundle/Smoke/Http/RouteConfigCustomization.php) class)
    * *Note: This is now achievable as the configuration class is located in the open box project-base. 
    However, that makes the upgrading of the component harder so the configuration is planned to be re-worked.*
* [Implementing custom product feed or modifying an existing one](/docs/introduction/product-feeds.md)
* [Extending the application using standard Symfony techniques](https://symfony.com/doc/current/bundles/override.html)
    * e.g. overriding Twig templates, routes, services, ...
* open-box modifications in `project-base`
    * e.g. adding new entities, changing the FE design, customization of FE javascripts, adding new FE pages (routes and controllers), ...

## Which issues are going to be addressed soon
* Adding a new page into the administration (including adding a new item into the admin menu)
* Extending data fixtures (including performance data fixtures)
* Extending data grids in the administration
* Extending all forms in the administration without the need of the template overriding
* Hiding the existing features and functionality

## What is not achievable
* Removing an attribute from a framework entity
* Changing a data type of an entity attribute or association
* Removing existing entities and features