# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.3.0] - 2018-02-19
### Changed
- services.yml updated to Symfony 3.4 best practices (@TomasLudvik)
- updated phpunit/phpunit to version 7 (@simara-svatopluk)

## [0.2.2] - 2017-10-04
### Added
- support for [shopsys/product-feed-interface](shopsys/product-feed-interface) 0.5.0 (@PetrHeinz)

## [0.2.1] - 2017-09-25
### Added
- [CONTRIBUTING.md](CONTRIBUTING.md) (@vitek-rostislav)
- [template for github pull requests](docs/PULL_REQUEST_TEMPLATE.md) (@vitek-rostislav)

### Changed
- Dependency [product-feed-interface](shopsys/product-feed-interface) upgraded from ~0.3.0 to ~0.4.0 (@MattCzerner)

## [0.2.0] - 2017-09-12
### Added
- This Changelog (@vitek-rostislav)
- New dependencies for dev(@MattCzerner)
    - phpunit/phpunit >=5.0.0,<6.0
    - twig/twig 1.34.0
    - twig/extensions 1.3.0
- New automatic test that is controlling right behaviour of plugin (@MattCzerner)
- Added travis build icon into [README.md](README.md) (@MattCzerner)
### Changed
- Dependency [plugin-interface](shopsys/product-feed-interface) upgraded from ~0.2.0 to ~0.3.0 (@MattCzerner)
### Removed
- `HeurekaDeliveryFeedConfig::getFeedItemRepository()` (@MattCzerner)

## [0.1.1] - 2017-08-18
### Fixed
- Usage of `FeedItemInterface::getId()` method in `feed.xml.twig` (@PetrHeinz)
    - it was renamed from `FeedItemInterface::getItemId()` in [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface) v0.2.0

## 0.1.0 - 2017-08-10
### Added
- Extracted Heureka product delivery feed plugin from [Shopsys Framework](http://www.shopsys-framework.com/) (@vitek-rostislav)
- `.travis.yml` file with Travis CI configuration (@vitek-rostislav)

[Unreleased]: https://github.com/shopsys/product-feed-heureka-delivery/compare/v0.3.0...HEAD
[0.3.0]: https://github.com/shopsys/product-feed-heureka-delivery/compare/v0.2.2...v0.3.0
[0.2.1]: https://github.com/shopsys/product-feed-heureka-delivery/compare/v0.2.1...v0.2.2
[0.2.1]: https://github.com/shopsys/product-feed-heureka-delivery/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/shopsys/product-feed-heureka-delivery/compare/v0.1.1...v0.2.0
[0.1.1]: https://github.com/shopsys/product-feed-heureka-delivery/compare/v0.1.0...v0.1.1
