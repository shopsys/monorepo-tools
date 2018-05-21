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
### [shopsys/shopsys]
#### Added
- [#108 - demo entity extension](https://github.com/shopsys/shopsys/pull/108): 
    - [cookbook](docs/cookbook/adding-new-attribute-to-an-entity.md) for adding new attribute to an entity

#### Changed
- [#110 - PHP-FPM Docker image tweaked for easier usage](https://github.com/shopsys/shopsys/pull/110):
    - PHP-FPM Docker image tweaked for easier usage
    - switched to Docker image `php:7.2-fpm-alpine` instead of `phpdockerio/php72-fpm:latest`
            - official PHP Docker image is much more stable and provides tags other than `latest`
            - built on Alpine linux which uses `apk` instead of `apt-get`
            - in the container there is no `bash` installed, use `sh` instead
    - all installation guides verified and tweaked
        - Docker installation supported on Linux, MacOS and Windows 10 Pro and higher (recommended way of installing the application)
        - native installation is also supported (recommended on Windows 10 Home and lower)
    - as a rule, using minor versions of docker images (eg. `1.2` or `1.2-alpine`) if possible
    - docs and `docker-compose.yml` templates reflect [changes of Docker images in shopsys/project-base]
    - `docker-compose-win.yml.dist` created for Windows OS which creates local volume because of permission problems with
        `postgresql` mounting
    - docs: changed `./phing` instruction code with `php phing` to make it work with all operating systems

#### Fixed
- [#117 - documentation: missing redis extension in required php extensions](https://github.com/shopsys/shopsys/pull/117) [@pk16011990]
- [#124 - Admin: Customer cannot be saved + fixed js error from administration console](https://github.com/shopsys/shopsys/pull/124): 
    - admin: e-mail validation in customer editation is working correctly now

## 7.0.0-alpha1 - 2018-04-12
### Added
- basic infrastructure so that the monorepo can be installed and used as standard application (@Miroslav-Stopka)
    - for details see [the Monorepo article](./docs/introduction/monorepo.md#infrastructure) in documentation
- [Shopsys Community License](./LICENSE)
- documentation was moved from [shopsys/project-base](https://github.com/shopsys/project-base) (@Miroslav-Stopka)

[Unreleased]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha1...HEAD
[shopsys/shopsys]: https://github.com/shopsys/shopsys
[shopsys/project-base]: https://github.com/shopsys/project-base
[shopsys/framework]: https://github.com/shopsys/framework
[shopsys/product-feed-zbozi]: https://github.com/shopsys/product-feed-zbozi
[shopsys/product-feed-google]: https://github.com/shopsys/product-feed-google
[shopsys/product-feed-heureka]: https://github.com/shopsys/product-feed-heureka
[shopsys/product-feed-heureka-delivery]: https://github.com/shopsys/product-feed-heureka-delivery
[shopsys/product-feed-interface]: https://github.com/shopsys/product-feed-interface
[shopsys/plugin-interface]: https://github.com/shopsys/plugin-interface
[shopsys/coding-standards]: https://github.com/shopsys/coding-standards
[shopsys/http-smoke-testing]: https://github.com/shopsys/http-smoke-testing
[shopsys/form-types-bundle]: https://github.com/shopsys/form-types-bundle
[shopsys/migrations]: https://github.com/shopsys/migrations
[shopsys/monorepo-tools]: https://github.com/shopsys/monorepo-tools

[@pk16011990]: https://github.com/pk16011990
