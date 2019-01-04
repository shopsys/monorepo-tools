# [Upgrade from 7.0.0-alpha2 to 7.0.0-alpha3](https://github.com/shopsys/shopsys/compare/v7.0.0-alpha2...v7.0.0-alpha3)

This guide contains instructions to upgrade from version 7.0.0-alpha2 to 7.0.0-alpha3.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
- classes in src/Components were revised, refactored and some of them were moved to model,
    for upgrading to newer version, you must go through commits done in [#272](https://github.com/shopsys/shopsys/pull/272) and reflect the changes of namespaces.
- FriendlyUrlToGenerateRepository: deleted. If you want to define your own data for friendly url generation, do it so by
    implementing the FriendlyUrlDataProviderInterface and tag your service with `shopsys.friendly_url_provider`.
- check changes in src/Model, all *editData*.php were merged into its *Data*.php relatives
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue
- access multi-domain attributes of entities via their main entity (instead of the usual entity detail)
    - entity domains (eg. `BrandDomain`) should be created, edited and directly accessed only in their main entities (eg. `Brand`)
    - see [#165 Different approach to multidomain entities](https://github.com/shopsys/shopsys/pull/165) for details
- `DomainsType` now uses array of booleans indexed by domain IDs instead of array of domain IDs, original behavior can be restored by adding model data transformer `IndexedBooleansToArrayOfIndexesTransformer`
- `CategoryDomain::$hidden` was changed to `CategoryDomain::$enabled` along with related methods (with negated value)
- `PaymentDomain` and `TransportDomain` are now created even for domains on which the entity should not be visible, check your custom queries that work with payments or transports
- instead of using `EntityManagerFacade::clear()` call `clear()` directly on the `EntityManager`
- all *Detail classes were removed:
    - use `CategoryWithLazyLoadedVisibleChildren` instead of `LazyLoadedCategoryDetail`
    - use `CategoryWithLazyLoadedVisibleChildrenFactory::createCategoriesWithLazyLoadedVisibleChildren()` instead of `CategoryDetailFactory::createLazyLoadedDetails()`
    - use `CategoryFacade::getCategoriesWithLazyLoadedVisibleChildrenForParent()` instead of `CategoryFacade::getVisibleLazyLoadedCategoryDetailsForParent()`
    - use `CategoryWithPreloadedChildren` instead of `CategoryDetail`
    - use `CategoryWithPreloadedChildrenFactory::createCategoriesWithPreloadedChildren()` instead of `CategoryDetailFactory::createDetailsHierarchy()`
    - use `CategoryFacade::getVisibleCategoriesWithPreloadedChildrenForDomain()` instead of `CategoryFacade::getVisibleCategoryDetailsForDomain()`
    - use `PaymentFacade::getIndependentBasePricesIndexedByCurrencyId()` instead of `PaymentDetail::$basePricesByCurrencyId`
    - use `TransportFacade::getIndependentBasePricesIndexedByCurrencyId()` instead of `TransportDetail::$basePricesByCurrencyId`
    - `ProductDetail::hasContentForDetailBox()` is not available anymore (it was useless)
    - use `ProductCachedAttributesFacade` for accessing product parameter values and selling price
    - in templates, use Twig function `getProductParameterValues(product)` instead of `productDetail.parameters`
    - in templates, use Twig function `getProductSellingPrice(product)` instead of `productDetail.sellingPrice`

## [shopsys/project-base]
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue

## [shopsys/coding-standards]
- create your custom `easy-coding-standard.yml` in your project root with your ruleset (you can use predefined ruleset as shown below)
- in order to run all checks, there is new unified way - execute `php vendor/bin/ecs check /path/to/project`
- see [EasyCodingStandard docs](https://github.com/Symplify/EasyCodingStandard#usage) for more information
### Example of custom configuration file
```yaml
#easy-coding-standard.yml
imports:
    - { resource: '%vendor_dir%/shopsys/coding-standards/easy-coding-standard.yml' }
parameters:
    exclude_files:
        - '*/ignored_folder/*'
    skip:
        ObjectCalisthenics\Sniffs\Files\FunctionLengthSniff:
            - '*/src/file.php'
```

## [shopsys/product-feed-google]
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue

## [shopsys/product-feed-heureka]
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue

## [shopsys/product-feed-heureka-delivery]
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue

## [shopsys/product-feed-zbozi]
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue

[shopsys/framework]: https://github.com/shopsys/framework
[shopsys/project-base]: https://github.com/shopsys/project-base
[shopsys/coding-standards]: https://github.com/shopsys/coding-standards
[shopsys/product-feed-google]: https://github.com/shopsys/product-feed-google
[shopsys/product-feed-heureka]: https://github.com/shopsys/product-feed-heureka
[shopsys/product-feed-heureka-delivery]: https://github.com/shopsys/product-feed-heureka-delivery
[shopsys/product-feed-zbozi]: https://github.com/shopsys/product-feed-zbozi
