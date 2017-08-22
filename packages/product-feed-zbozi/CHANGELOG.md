# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- This Changelog (@vitek-rostislav)

## [0.2.0] - 2017-08-08
### Added
- Retrieving custom plugin data and extension of product form with custom fields (@PetrHeinz)
    - show (offer in feeds)
    - cpc (maximum price per click)
    - cpc_search (maximum price per click in offers)
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
- Extracted Zboží.cz product feed plugin from [Shopsys Framework](http://www.shopsys-framework.com/) (@PetrHeinz)
- `.travis.yml` file with Travis CI configuration (@PetrHeinz)

[Unreleased]: https://github.com/shopsys/product-feed-zbozi/compare/v0.2.0...HEAD
[0.2.0]: https://github.com/shopsys/product-feed-zbozi/compare/v0.1.0...v0.2.0
