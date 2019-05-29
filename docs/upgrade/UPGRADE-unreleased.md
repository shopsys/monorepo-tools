# [Upgrade from v7.2.1 to Unreleased](https://github.com/shopsys/shopsys/compare/v7.2.1...HEAD)

This guide contains instructions to upgrade from version v7.2.1 to Unreleased.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]

### Application
- follow instructions in [the separate article](upgrade-instructions-for-read-model-for-product-lists.md) to introduce read model for frontend product lists into your project ([#1018](https://github.com/shopsys/shopsys/pull/1018))
    - we recommend to read [Introduction to Read Model](/docs/model/introduction-to-read-model.md) article

### Tools
- we recommend upgrading PHPStan to level 4 [#1040](https://github.com/shopsys/shopsys/pull/1040)
    - you'll find detailed instructions in separate article [Upgrade Instructions for Upgrading PHPStan to Level 4](/docs/upgrade/phpstan-level-4.md)

[shopsys/framework]: https://github.com/shopsys/framework
