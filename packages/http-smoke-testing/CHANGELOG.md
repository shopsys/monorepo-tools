# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Changed
- added support of phpunit/phpunit ^6.0 and ^7.0 (@simara-svatopluk)

## [1.1.0] - 2017-11-01
### Added
- [CONTRIBUTING.md](CONTRIBUTING.md) (@vitek-rostislav)
- [template for github pull requests](docs/PULL_REQUEST_TEMPLATE.md) (@vitek-rostislav)

### Changed
- Improved IDE auto-completion when customizing test cases via [`RouteConfig`](src/RouteConfig.php) (@PetrHeinz)
    - Methods `changeDefaultRequestDataSet()` and `addExtraRequestDataSet()` now return new interface [`RequestDataSetInterface`](src/RequestDataSetConfig.php).
    - This new interface includes only a subset of methods in [`RequestDataSet`](src/RequestDataSet.php) that is relevant to test case customization.

## [1.0.1] - 2017-07-03
### Added
- Unit test for RequestDataSetGenerator class (@MattCzerner)
- This Changelog (@PetrHeinz)

## 1.0.0 - 2017-05-23
### Added
- Extracted HTTP smoke testing functionality from [Shopsys Framework](http://www.shopsys-framework.com/) (@PetrHeinz)
- `.travis.yml` file with Travis CI configuration (@PetrHeinz)

[Unreleased]: https://github.com/shopsys/http-smoke-testing/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/shopsys/http-smoke-testing/compare/v1.0.0...v1.1.0
[1.0.1]: https://github.com/shopsys/http-smoke-testing/compare/v1.0.0...v1.0.1
