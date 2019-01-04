# [Upgrade from v7.0.0-beta3 to v7.0.0-beta4](https://github.com/shopsys/shopsys/compare/v7.0.0-beta3...v7.0.0-beta4)

This guide contains instructions to upgrade from version v7.0.0-beta3 to v7.0.0-beta4.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
### Infrastructure
- set `ENV COMPOSER_MEMORY_LIMIT=-1` in base stage in your `docker/php-fpm/Dockerfile` as composer consumes huge amount of memory during dependencies installation ([#635](https://github.com/shopsys/shopsys/pull/635/files))

### Configuration
- *(optional)* modify your `src/Shopsys/ShopBundle/Resources/config/services.yml` ([#616](https://github.com/shopsys/shopsys/pull/616))
    - change the resource for automatic registration of Model services from `resource: '../../Model/**/*{Facade,Factory}.php'` to `resource: '../../Model/**/*{Facade,Factory,Repository}.php'`

[shopsys/framework]: https://github.com/shopsys/framework
