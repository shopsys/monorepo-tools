# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- [CONTRIBUTING.md](CONTRIBUTING.md) (@vitek-rostislav)

## [0.2.0] - 2017-09-06
### Added
- This Changelog (@vitek-rostislav)
- interface for loading plugin's demo data (@MattCzerner)
    - `PluginDataFixtureInterface`

## 0.1.0 - 2017-08-04
### Added
- Package of interfaces providing compatibility between [Shopsys Framework](https://www.shopsys-framework.com) and plugins (@PetrHeinz)
    - interfaces responsible for retrieving and saving plugin custom data
        - `DataStorageInterface`
        - `PluginDataStorageProviderInterface`
    - interface responsible for extending CRUD with plugin custom sub-forms
        - `PluginCrudExtensionInterface`
- `.travis.yml` file with Travis CI configuration (@PetrHeinz)

[Unreleased]: https://github.com/shopsys/plugin-interface/compare/v0.2.0...HEAD
[0.2.0]: https://github.com/shopsys/plugin-interface/compare/v0.1.0...v0.2.0
