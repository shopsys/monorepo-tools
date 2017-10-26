# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0-beta.18.0] - 2017-10-19
### Added
- [coding standards documentation](docs/contributing/coding-standards.md) (@vitek-rostislav)
- acceptance tests asserting successful image upload in admin for product, transport and payment (@vitek-rostislav)
- Docker based server stack for easier installation and development (@TomasLudvik)
    - see [Docker Installation Guide](docs/introduction/installation-using-docker.md) for details
- plugins can now extend the CRUD of categories (using `CategoryFormType`) (@MattCzerner)

### Changed
- cache deletion before running unit tests is now done using `Symfony\Filesystem` instead of using console command (@TomasLudvik)
    - deleting via console command `cache:clear` is slow, because it creates whole application container first and then deletes all cache created in process
- Windows locales list: use more tolerant name for Czech locale (@vitek-rostislav)
    - in Windows 2017 Fall Creators Update the locale name was changed from "Czech_Czech Republic" to "Czech_Czechia"
    - name "Czech" is acceptable in all Windows versions
- interfaces for CRON modules moved to [shopsys/plugin-interface](https://github.com/shopsys/plugin-interface) (@MattCzerner)
- `ImageDemoCommand` now prompts to truncate "images" db table when it is not empty before new demo images are loaded (@vitek-rostislav)

### Deleted
- logic of Heureka categorization moved to [shopsys/product-feed-heureka](https://github.com/shopsys/product-feed-heureka) (@MattCzerner)
    - all your current Heureka category data will be migrated into the new structure

### Fixed
- proper `baseUrl` value from `domains_urls.yaml` is now stored into `settings` when creating new domain (@vitek-rostislav)

## [2.0.0-beta.17.0] - 2017-10-03
### Added
- MIT license (@TomasLudvik)
- phing targets `eslint-check`, `eslint-check-diff`, `eslint-fix` and `eslint-fix-diff` to check and fix coding standards in JS files (@sspooky13)
    - executed as a part of targets `standards`, `standards-diff`, `standards-fix` and `standards-fix-diff`
- [product feed plugin for Google](https://github.com/shopsys/product-feed-google/) (@MattCzerner)
- new article explaining [Basics About Package Architecture](docs/introduction/basics-about-package-architecture.md) (@vitek-rostislav)

### Changed
- [`StandardFeedItemRepository`](src/Shopsys/ShopBundle/Model/Feed/Standard/StandardFeedItemRepository.php): now selects available products instead of sellable, filtering of not sellable products is made in product plugins (@MattCzerner)
- implementations of `StandardFeedItemInterface` now must have implemented methods `isSellingDenied()` and `getCurrencyCode()`(@MattCzerner)
- implementations of `FeedConfigInterface` now must have implemented method `getAdditionalInformation()` (@MattCzerner)

## [2.0.0-beta.16.0] - 2017-09-19
### Added
- new command `shopsys:plugin-data-fixtures:load` for loading demo data from plugins (@MattCzerner)
    - called during build of demo database
- new documentation about Shopsys Framework model architecture (@TomasLudvik)
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
- sender email for error reporting is now configured in [`parameters.yml.dist`](app/config/parameters.yml.dist) (@vitek-rostislav)
- reimplemented [`CategoriesType`](src/Shopsys/ShopBundle/Form/CategoriesType.php) (@Petr Heinz)
    - it now extends `CollectionType` instead of `ChoiceType`
    - it loads only those categories that are needed to show all selected categories in a tree, not all of them
    - collapsed categories can be loaded via AJAX
- [`CategoryRepository::findById()`](src/Shopsys/ShopBundle/Model/Category/CategoryRepository.php) now uses `find()` method of Doctrine repository instead of query builder so it can use cached results (@PetrHeinz)
- it is possible to mention occurrences of an image size in [`images.yml`](src/Shopsys/ShopBundle/Resources/config/images.yml) (@PetrHeinz)
    - previously they were directly in [`ImageController`](src/Shopsys/ShopBundle/Controller/Admin/ImageController.php)
    - they are not translatable anymore (too hard to maintain)

### Removed
- email for error reporting removed from [`parameters_test.yml.dist`](app/config/parameters_test.yml.dist) (@vitek-rostislav)
- removed unused private properties from classes (@PetrHeinz)
- removed `CategoriesTypeTransformerFactory` (@PetrHeinz)
    - the `CategoriesTypeTransformer` can be fully autowired after deletion of `$domainId`

### Fixed
- [`InlineEditPage::createNewRow()`](tests/ShopBundle/Acceptance/acceptance/PageObject/Admin/InlineEditPage.php) now waits for AJAX to complete (@PetrHeinz)
    - fixes false negatives of acceptance test [`PromoCodeInlineEditCest::testPromoCodeCreate()`](tests/ShopBundle/Acceptance/acceptance/PromoCodeInlineEditCest.php)

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

[Unreleased]: https://git.shopsys-framework.com/shopsys/shopsys-framework/compare/v2.0.0-beta.18.0...HEAD
[2.0.0-beta.18.0]: https://git.shopsys-framework.com/shopsys/shopsys-framework/compare/v2.0.0-beta.17.0...v2.0.0-beta.18.0
[2.0.0-beta.17.0]: https://git.shopsys-framework.com/shopsys/shopsys-framework/compare/v2.0.0-beta.16.0...v2.0.0-beta.17.0
[2.0.0-beta.16.0]: https://git.shopsys-framework.com/shopsys/shopsys-framework/compare/v2.0.0-beta.15.0...v2.0.0-beta.16.0 
