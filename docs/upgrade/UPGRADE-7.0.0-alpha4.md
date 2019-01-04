# [Upgrade from 7.0.0-alpha3 to 7.0.0-alpha4](https://github.com/shopsys/shopsys/compare/v7.0.0-alpha3...v7.0.0-alpha4)

This guide contains instructions to upgrade from version 7.0.0-alpha3 to 7.0.0-alpha4.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
- move creation of data objects into factories
- already existing data object factories changed their signatures
- to change the last item in admin breadcrumb, use `BreadcrumbOverrider:overrideLastItem(string $label)` instead of `Breadcrumb::overrideLastItem(MenuItem $item)`
- if you've customized the admin menu by using your own `admin_menu.yml`, implement event listeners instead
    - see the [Adding a New Administration Page](/docs/cookbook/adding-a-new-administration-page.md) cookbook for details

## [shopsys/product-feed-google]
- move creation of data objects into factories
- already existing data object factories changed their signatures

## [shopsys/product-feed-heureka]
- move creation of data objects into factories
- already existing data object factories changed their signatures

## [shopsys/product-feed-zbozi]
- move creation of data objects into factories
- already existing data object factories changed their signatures

[shopsys/framework]: https://github.com/shopsys/framework
[shopsys/product-feed-google]: https://github.com/shopsys/product-feed-google
[shopsys/product-feed-heureka]: https://github.com/shopsys/product-feed-heureka
[shopsys/product-feed-zbozi]: https://github.com/shopsys/product-feed-zbozi
