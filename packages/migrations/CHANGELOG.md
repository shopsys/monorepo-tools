# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
- We are releasing the Shopsys Framework in version 7 and we are synchronizing versions because
  the Shopsys Framework and all packages are now developed together and are now same-version compatible.

### Changed
- updated phpunit/phpunit to version 7 (@simara-svatopluk)
- DB migrations are installed from all registered bundles (@TomasLudvik)
    - they should be located in directory "Migrations" in the root of the bundle

## [2.3.0] - 2018-02-19
### Added
- This Changelog (@TomasLudvik)

### Changed
- services.yml updated to Symfony 3.4 best practices (@TomasLudvik)

[Unreleased]: https://github.com/shopsys/migrations/compare/v2.3.0...HEAD
[2.3.0]: https://github.com/shopsys/migrations/compare/v2.2.0...v2.3.0
