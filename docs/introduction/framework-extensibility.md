# Framework Extensibility

This article summarizes the current possibilities of the framework extension,
provides a list of customizations that are not achievable now but are planned to be enabled soon,
as well as a list of customizations that are not (and will not be) possible at all.

## What is achievable easily
* [Extending an entity](/docs/wip_glassbox/entity-extension.md)
    * [Adding a new attribute](/docs/cookbook/adding-new-attribute-to-an-entity.md)
* The administration can be extended by:
    * [Adding a new administration page](/docs/cookbook/adding-a-new-administration-page.md) along with the side menu and breadcrumbs
    * [Extending particular forms](/docs/wip_glassbox/form-extension.md) without the need of the template overriding
* [Customizing database migrations](/docs/introduction/database-migrations.md)
    * adding a new migration as well as skipping and reordering the existing ones
* Configuring the smoke tests (see [`RouteConfigCustomization`](/project-base/tests/ShopBundle/Smoke/Http/RouteConfigCustomization.php) class)
    * *Note: This is now achievable as the configuration class is located in the open box project-base.
    However, that makes the upgrading of the component harder so the configuration is planned to be re-worked.*
* [Implementing custom product feed or modifying an existing one](/docs/introduction/product-feeds.md)
* [Implementing a basic data import](/docs/cookbook/basic-data-import.md) to import data to you e-shop from an external source
    * adding a new cron module and configuring it
* [Extending the application using standard Symfony techniques](https://symfony.com/doc/current/bundles/override.html)
    * e.g. overriding Twig templates, routes, services, ...
* [Adding a new advert position](/docs/cookbook/adding-a-new-advert-position.md) to be used in the administration section *Marketing > Advertising system*
* open-box modifications in `project-base`
    * e.g. adding new entities, changing the FE design, customization of FE javascripts, adding new FE pages (routes and controllers), ...
* [Hiding the existing features and functionality](https://github.com/shopsys/demoshop/pull/13)
* adding a new javascript into admin
    * add the new javascript files into the directory `src/Shopsys/ShopBundle/Resources/scripts/custom_admin` and they will be loaded automatically

## What is achievable with additional effort

* Extending factories and controllers - [see commit in demoshop](https://github.com/shopsys/demoshop/commit/898d111879aef40196f79ac763373560f44aef59#diff-1b3bd68670cd376165cdc6cfc634f24f)
* Adding form option into existing form - [see commit in demoshop](https://github.com/shopsys/demoshop/commit/898d111879aef40196f79ac763373560f44aef59#diff-3293b000b06ad6c0280341584c4d661d)
* Extending administration form theme - [see commit in demoshop](https://github.com/shopsys/demoshop/commit/d0e0eaaa2eeac5e1c90d8a29be5c827c4a067b9f)
* Changing an entity association - [see commit in demoshop](https://github.com/shopsys/demoshop/commit/9931083ea37ad611568e32bc1a9c8cf203401809) [*and actual association change*](https://github.com/shopsys/demoshop/commit/f3884368289da4b7c5eb1cee3078c9ec69c933dc)
    * this change is complicated and potentially dangerous

## Which issues are going to be addressed soon
* Extending data fixtures (including performance data fixtures)
* Extending data grids in the administration
* Extending all forms in the administration without the need of the template overriding
* Extending classes like Repositories without the need for changing the project-base tests

## What is not supported
* Removing an attribute from a framework entity
* Changing a data type of an entity attribute
* Removing existing entities and features

## Successfully implemented features
* [Shipping method with pickup places](https://github.com/shopsys/demoshop/pull/6)
    * new shipping method Zasilkovna
    * pick up places are downloaded by cron
    * order process change
    * details in a [issue description](https://github.com/shopsys/demoshop/issues/3)
* [Product attribute "condition"](https://github.com/shopsys/demoshop/pull/7)
    * product entity extension
    * administration form extension
    * frontend product change
    * google feed change
    * detailed info in a [issue description](https://github.com/shopsys/demoshop/issues/4)
* [Second description of a category](https://github.com/shopsys/demoshop/pull/8)
    * category entity extension
    * administration form extension
        * new multidomain
    * frontend product list change
    * detailed info in a [issue description](https://github.com/shopsys/demoshop/issues/5)
* [Twig templates cache](https://github.com/shopsys/demoshop/pull/9)
    * performance improved by ~15%
    * cache is invalidated every 5 minutes
* [Hidden the functionality of the flags](https://github.com/shopsys/demoshop/pull/13)
    * hidden functionality in administration
    * hidden functionality in frontend
    * flags do not affect shop at all
* [Company account with multiple users](https://github.com/shopsys/demoshop/pull/15)
    * group user accounts under one company account
    * separate users login credentials
    * share company attributes
    * change association from 1:1 to 1:N
