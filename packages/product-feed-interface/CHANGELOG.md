# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
- We are releasing the Shopsys Framework in version 7 and we are synchronizing versions because
  the Shopsys Framework and all packages are now developed together and are now same-version compatible.

### Removed
- `HeurekaCategoryNameProviderInterface` as it is specific to Heureka product feed (@PetrHeinz)
    - [shopsys/product-feed-heureka](https://github.com/shopsys/product-feed-heureka) manages Heureka categories on its own since v0.5.0

## [0.5.0] - 2017-10-04
- [`StandardFeedItemInterface`](src/StandardFeedItemInterface.php) contains ID of its main category (@MattCzerner)

## [0.4.0] - 2017-09-25
### Added
- [CONTRIBUTING.md](CONTRIBUTING.md) (@vitek-rostislav)
- [template for github pull requests](docs/PULL_REQUEST_TEMPLATE.md) (@vitek-rostislav)
- [`StandardFeedItemInterface`](src/StandardFeedItemInterface.php) has new method `isSellingDenied()` (@MattCzerner)
- [`FeedConfigInterface`](src/FeedConfigInterface.php) has new method `getAdditionalInformation()` (@MattCzerner)
- [`StandardFeedItemInterface`](src/StandardFeedItemInterface.php) has new method `getCurrencyShortcut()` (@MattCzerner)

## [0.3.0] - 2017-09-12
### Added
- This Changelog (@vitek-rostislav)
- UPGRADE.md (@vitek-rostislav)
### Removed
- `FeedItemRepositoryInterface` (@vitek-rostislav)
- `FeedConfigInterface::getFeedItemRepository()` (@vitek-rostislav)

## [0.2.1] - 2017-08-17
### Added
- New interface for delivery feed items - `DeliveryFeedItemInterface` (@vitek-rostislav)

## [0.2.0] - 2017-08-07
### Changed
- `FeedItemInterface`: renamed method `getItemId()` to `getId()` (@PetrHeinz)
- `FeedItemCustomValuesProviderInterface` renamed to `HeurekaCategoryNameProviderInterface` (@PetrHeinz)
### Removed
- General data storage functionality extracted into separate package [shopsys/plugin-interface](https://github.com/shopsys/plugin-interface) (@PetrHeinz)
    - removed `FeedItemCustomValuesProviderInterface::getCustomValuesForItems()` and `FeedItemCustomValuesInterface`

## 0.1.0 - 2017-07-13
### Added
- Package of interfaces providing compatibility between [Shopsys Framework](https://www.shopsys-framework.com) and product feed plugins. (@PetrHeinz)
- `.travis.yml` file with Travis CI configuration (@PetrHeinz)

[Unreleased]: https://github.com/shopsys/product-feed-interface/compare/v0.5.0...HEAD
[0.5.0]: https://github.com/shopsys/product-feed-interface/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/shopsys/product-feed-interface/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/shopsys/product-feed-interface/compare/v0.2.1...v0.3.0
[0.2.1]: https://github.com/shopsys/product-feed-interface/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/shopsys/product-feed-interface/compare/v0.1.0...v0.2.0
