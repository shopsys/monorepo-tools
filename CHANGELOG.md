# Changelog
All notable changes, that change in some way the behavior of monorepo and do not interfere only with a particular package, will be documented in this file.

Changes, that change the specific package or project-base, will be documented in the changelog of modified package.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Changelogs of repositories maintained by monorepo

* [shopsys/project-base](./project-base/CHANGELOG.md)
* [shopsys/framework](./packages/framework/CHANGELOG.md)
* [shopsys/product-feed-zbozi](./packages/product-feed-zbozi/CHANGELOG.md)
* [shopsys/product-feed-google](./packages/product-feed-google/CHANGELOG.md)
* [shopsys/product-feed-heureka](./packages/product-feed-heureka/CHANGELOG.md)
* [shopsys/product-feed-heureka-delivery](./packages/product-feed-heureka-delivery/CHANGELOG.md)
* [shopsys/product-feed-interface](./packages/product-feed-interface/CHANGELOG.md)
* [shopsys/plugin-interface](./packages/plugin-interface/CHANGELOG.md)
* [shopsys/coding-standards](./packages/coding-standards/CHANGELOG.md)
* [shopsys/http-smoke-testing](./packages/http-smoke-testing/CHANGELOG.md)
* [shopsys/form-types-bundle](./packages/form-types-bundle/CHANGELOG.md)
* [shopsys/migrations](./packages/migrations/CHANGELOG.md)
* [shopsys/monorepo-tools](./packages/monorepo-tools/CHANGELOG.md)

## [Unreleased]
### Changed
- all installation guides verified and tweaked (@TomasLudvik)
    - Docker installation supported on Linux, MacOS and Windows 10 Pro and higher (recommended way of installing the application)
    - native installation is also supported (recommended on Windows 10 Home and lower)
- as a rule, using minor versions of docker images (eg. `1.2` or `1.2-alpine`) if possible (@MattCzerner)

## 7.0.0-alpha1 - 2018-04-12
### Added
- basic infrastructure so that the monorepo can be installed and used as standard application (@Miroslav-Stopka)
    - for details see [the Monorepo article](./docs/introduction/monorepo.md#infrastructure) in documentation
- [Shopsys Community License](./LICENSE)
- documentation was moved from [shopsys/project-base](https://github.com/shopsys/project-base) (@Miroslav-Stopka)

[Unreleased]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha1...HEAD
