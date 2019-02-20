# [Upgrade from v7.0.0-beta6 to Unreleased]

This guide contains instructions to upgrade from version v7.0.0-beta6 to Unreleased.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
### Tools
- *(low priority)* add `product-search-export-products` as a dependency of `build-demo` phing target in your `build.xml`
if you want to have products data exported to Elasticsearch after `build-demo` target is run ([#824](https://github.com/shopsys/shopsys/pull/824/files))

### Application
- *(low priority)* if you want to test behavior of cart with no listable product in it, implement functional test as it is in [#852](https://github.com/shopsys/shopsys/pull/852)

[Upgrade from v7.0.0-beta6 to Unreleased]: https://github.com/shopsys/shopsys/compare/v7.0.0-beta6...HEAD
[shopsys/framework]: https://github.com/shopsys/framework
