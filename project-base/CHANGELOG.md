# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- new command `shopsys:plugin-data-fixtures:load` for loading demo data from plugins (@MattCzerner)
    - called during build of demo database
- new documentation abuout Shopsys Framework model architecture (@TomasLudvik)
- `FeedItemRepositoryInterface` (@vitek-rostislav)
    - moved from [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/)
- [template for github pull requests](docs/PULL_REQUEST_TEMPLATE.md) (@vitek-rostislav)

### Changed
- dependency [shopsys/plugin-interface](https://github.com/shopsys/plugin-interface/) upgraded from 0.1.0 to 0.2.0 (@MattCzerner)
- dependency [shopsys/product-feed-heureka](https://github.com/shopsys/product-feed-heureka/) upgraded from 0.2.0 to 0.4.0 (@MattCzerner)
- dependency [shopsys/product-feed-zbozi](https://github.com/shopsys/product-feed-zbozi/) upgraded from 0.2.0 to 0.4.0 (@MattCzerner)
- dependency [shopsys/product-feed-heureka-delivery](https://github.com/shopsys/product-feed-heureka-delivery/) upgraded from 0.1.1 to 0.2.0 (@vitek-rostislav)
- dependency [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/) upgraded from 0.2.1 to 0.3.0 (@vitek-rostislav)
- it is no longer needed to redeclare feed plugin's implementations of `FeedConfigInterface` in `services.yml` (@vitek-rostislav)
    - decision about providing proper instance of `FeedItemRepositoryInterface` is made in `FeedConfigFacade`
- FeedConfigRepository renamed to [`FeedConfigRegistry`](src/Shopsys/ShopBundle/Model/Feed/FeedConfigRegistry.php) (@MattCzerner)
    - it is not fetching data from Doctrine as other repositories, it only serves as a container for registering services of specific type
    - similar to [`PluginDataFixtureRegistry`](src/Shopsys/ShopBundle/Component/Plugin/PluginDataFixtureRegistry.php) or [`PluginCrudExtensionRegistry`](src/Shopsys/ShopBundle/Component/Plugin/PluginCrudExtensionRegistry.php)
- `UknownPluginDataFixtureException` renamed to [`UnknownPluginCrudExtensionTypeException`](src/Shopsys/ShopBundle/Component/Plugin/Exception/UnknownPluginCrudExtensionTypeException.php) because of a typo (@MattCzerner)
- [`FeedConfigRegistry`](src/Shopsys/ShopBundle/Model/Feed/FeedConfigRegistry.php) now contains all FeedConfigs in one array (indexed by type) (@vitek-rostislav)
    - definition and assertion of known feed configs types moved from [`RegisterProductFeedConfigsCompilerPass`](src/Shopsys/ShopBundle/DependencyInjection/Compiler/RegisterProductFeedConfigsCompilerPass.php) to [`FeedConfigRegistry`](src/Shopsys/ShopBundle/Model/Feed/FeedConfigRegistry.php)
    - changed message and arguments of [`UnknownFeedConfigTypeException`](src/Shopsys/ShopBundle/Model/Feed/Exception/UnknownFeedConfigTypeException.php)
- renamed methods working with standard feeds only to be more expressive (@PetrHeinz)
    - renamed `FeedConfigFacade::getFeedConfigs()` to `getStandardFeedConfigs()`
    - renamed `FeedFacade::generateFeedsIteratively()` to `generateStandardFeedsIteratively()`
    - renamed `FeedGenerationConfigFactory::createAll()` to `createAllForStandardFeeds()`
- [`parameters.yml.dist`](app/config/parameters.yml.dist): renamed parameter `email_for_error_reporting` to `error_reporting_email_to` (@vitek-rostislav)

### Removed
- email for error reporting removed from [`parameters_test.yml.dist`](app/config/parameters_test.yml.dist) (@vitek-rostislav)

## 2.0.0-beta.15.0 - 2017-08-31
- previous beta versions released only internally (mentioned changes since 1.0.0)

### Added
- PHP 7 support
- [a basic knowledgebase](docs/index.md)
    - installation guide
    - guidelines for contributions
    - cookbooks
    - articles on automated testing

### Changed
- update to Symfony 3
- PSR-2 compliance
- English as a main language
    - language of first front-end domain
    - language of administration
    - all translatable message sources in English

### Deleted
- separation of HTTP smoke test module into a component:
    - https://github.com/shopsys/http-smoke-testing/
- separation of product feed modules into plugins:
    - https://github.com/shopsys/plugin-interface/
    - https://github.com/shopsys/product-feed-interface/
    - https://github.com/shopsys/product-feed-heureka/
    - https://github.com/shopsys/product-feed-heureka-delivery/
    - https://github.com/shopsys/product-feed-zbozi/

## 1.0.0 - 2016-11-09
- developed since 2014-03-31
- used only as internal platform for e-commerce projects of Shopsys Agency
- released only internally

### Added
- product catalogue
- registered customers
- basic orders management
- back-end administration
- front-end fulltext search
- front-end product filtering
- 3-step ordering process
- products variants
- simple promo codes
- product feeds for product aggregators
- basic CMS
- multiple administrators
- support for several currencies
- support for several languages
- support for several domains
- full friendly URL for main entities
- customizable SEO attributes for main entities

[Unreleased]: https://git.shopsys-framework.com/shopsys/shopsys-framework/compare/v2.0.0-beta.15.0...HEAD
