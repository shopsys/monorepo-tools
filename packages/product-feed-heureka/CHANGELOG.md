# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Changed
- renamed [`TestStandardFeedItem`] to [`TestHeurekaStandardFeedItem`] (@Miroslav-Stopka)
- updated phpunit/phpunit to version 7 (@simara-svatopluk)

## [0.6.1] - 2018-02-19
### Changed
- updated package shopsys/form-types-bundle to version 0.2.0

## [0.6.0] - 2018-02-19
### Changed
- services.yml updated to Symfony 3.4 best practices

## [0.5.1] - 2017-10-06
- names of Heureka categories are now cached by category ID in [`HeurekaFeedConfig`](src/HeurekaFeedConfig.php) (@PetrHeinz)

## [0.5.0] - 2017-10-05
### Added
- logic of Heureka categorization moved from [Shopsys Framework](https://www.shopsys-framework.com/) core repository (@MattCzerner)
    - Heureka categories are downloaded everyday via CRON module
    - extends CRUD of categories for assigning Heureka categories to categories on your online store
    - contains demo data fixtures

## [0.4.2] - 2017-10-05
### Added
- support for shopsys/plugin-interface 0.3.0 (@PetrHeinz)
- support for shopsys/product-feed-interface 0.5.0 (@PetrHeinz)

## [0.4.1] - 2017-09-25
### Added
- [CONTRIBUTING.md](CONTRIBUTING.md) (@vitek-rostislav)
- [template for github pull requests](docs/PULL_REQUEST_TEMPLATE.md) (@vitek-rostislav)
### Changed
- Dependency [product-feed-interface](shopsys/product-feed-interface) upgraded from ~0.3.0 to ~0.4.0 (@MattCzerner)
- [`HeurekaFeedConfig`](src/HeurekaFeedConfig.php) now filters not sellable products (@MattCzerner)
- [`HeurekaFeedConfig`](src/HeurekaFeedConfig.php) implemented method `getAdditionalData()` (@MattCzerner)
- [`TestStandardFeedItem`](tests/TestStandardFeedItem.php) implemented method `getCurrencyCode()` (@MattCzerner)

## [0.4.0] - 2017-09-12
### Added
- New dependencies for dev(@MattCzerner)
    - phpunit/phpunit 5.7.21
    - twig/twig 1.34.0
    - twig/extensions 1.3.0
- New automatic test that is controlling right behaviour of plugin (@MattCzerner)
- Added travis build icon into [README.md](README.md) (@MattCzerner)
### Changed
- Dependency [product-feed-interface](shopsys/product-feed-interface) upgraded from ~0.2.0 to ~0.3.0 (@MattCzerner)
### Removed
- `HeurekaFeedConfig::getFeedItemRepository()` (@MattCzerner)

## [0.3.0] - 2017-08-09
### Added
- This Changelog (@vitek-rostislav)
- UPGRADE.md (@vitek-rostislav)
- Plugin demo data (cpc for 2 domains) (@MattCzerner)
### Changed
- Dependency [plugin-interface](shopsys/plugin-interface) upgraded from ~0.1.0 to ~0.2.0 (@MattCzerner)

## [0.2.0] - 2017-08-02
### Added
- Retrieving custom plugin data (@PetrHeinz)
    - Heureka category names
    - MAX_CPC (Maximum price per click)
- Extension of product form with custom field for MAX_CPC (@PetrHeinz)
- New dependencies (@PetrHeinz)
    - [shopsys/plugin-interface ~0.1.0](https://github.com/shopsys/plugin-interface)
    - [shopsys/form-types-bundle ~0.1.0](https://github.com/shopsys/form-types-bundle)
    - [symfony/form ^3.0](https://github.com/symfony/form)
    - [symfony/translation ^3.0](https://github.com/symfony/translation)
    - [symfony/validator ^3.0](https://github.com/symfony/validator)
### Changed
- Dependency [product-feed-interface](shopsys/product-feed-interface) upgraded from ~0.1.0 to ~0.2.0 (@PetrHeinz)

## 0.1.0 - 2017-07-13
### Added
- Extracted Heureka product feed plugin from [Shopsys Framework](http://www.shopsys-framework.com/) (@PetrHeinz)
- `.travis.yml` file with Travis CI configuration (@PetrHeinz)

[Unreleased]: https://github.com/shopsys/product-feed-heureka/compare/v0.6.1...HEAD
[0.6.1]: https://github.com/shopsys/product-feed-heureka/compare/v0.6.0...v0.6.1
[0.6.0]: https://github.com/shopsys/product-feed-heureka/compare/v0.5.1...v0.6.0
[0.5.1]: https://github.com/shopsys/product-feed-heureka/compare/v0.5.0...v0.5.1
[0.5.0]: https://github.com/shopsys/product-feed-heureka/compare/v0.4.2...v0.5.0
[0.4.2]: https://github.com/shopsys/product-feed-heureka/compare/v0.4.1...v0.4.2
[0.4.1]: https://github.com/shopsys/product-feed-heureka/compare/v0.4.0...v0.4.1
[0.4.0]: https://github.com/shopsys/product-feed-heureka/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/shopsys/product-feed-heureka/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/shopsys/product-feed-heureka/compare/v0.1.0...v0.2.0
