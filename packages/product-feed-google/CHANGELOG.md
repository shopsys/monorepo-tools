# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
- We are releasing the Shopsys Framework in version 7 and we are synchronizing versions because
  the Shopsys Framework and all packages are now developed together and are now same-version compatible.

### Changed
- renamed [`TestStandardFeedItem`] to [`TestGoogleStandardFeedItem`] (@Miroslav-Stopka)
- updated phpunit/phpunit to version 7 (@simara-svatopluk)

## [0.2.1] - 2018-02-19
### Fixed
- services.yml autodiscovery settings

## [0.2.0] - 2018-02-19
### Changed
- services.yml updated to Symfony 3.4 best practices

## [0.1.2] - 2018-02-12
### Fixed
- Fix availability value (@simara-svatopluk)

### Added
- [template for github pull requests](docs/PULL_REQUEST_TEMPLATE.md) (@stanoMilan)

## [0.1.1] - 2017-10-04
### Added
- support for shopsys/plugin-interface 0.3.0 (@PetrHeinz)
- support for shopsys/product-feed-interface 0.5.0 (@PetrHeinz)

## 0.1.0 - 2017-09-25
### Added
- added basic logic of product feed for Google (@MattCzerner)
- composer.json: added shopsys/coding-standards into require-dev (@MattCzerner)

[Unreleased]: https://github.com/shopsys/product-feed-google/compare/v0.2.1...HEAD
[0.2.1]: https://github.com/shopsys/product-feed-google/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/shopsys/product-feed-google/compare/v0.1.2...v0.2.0
[0.1.1]: https://github.com/shopsys/product-feed-google/compare/v0.1.0...v0.1.1
[0.1.2]: https://github.com/shopsys/product-feed-google/compare/v0.1.1...v0.1.2
