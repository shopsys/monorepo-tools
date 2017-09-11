# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- [CONTRIBUTING.md](CONTRIBUTING.md) (@vitek-rostislav)

## [0.3.0]
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

[Unreleased]: https://github.com/shopsys/product-feed-interface/compare/v0.3.0...HEAD
[0.3.0]: https://github.com/shopsys/product-feed-interface/compare/v0.2.1...v0.3.0
[0.2.1]: https://github.com/shopsys/product-feed-interface/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/shopsys/product-feed-interface/compare/v0.1.0...v0.2.0
