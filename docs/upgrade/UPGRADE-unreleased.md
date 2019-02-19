# [Upgrade from v7.0.0 to Unreleased]

This guide contains instructions to upgrade from version v7.0.0 to Unreleased.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
### Application
- *(low priority)* to add support of functional tests of Redis ([#846](https://github.com/shopsys/shopsys/pull/846))
    - download [`snc_redis.yml`](https://github.com/shopsys/project-base/blob/master/app/config/packages/test/snc_redis.yml) to `app/config/packages/test/snc_redis.yml`
    - download [`RedisFacadeTest.php`](https://github.com/shopsys/project-base/tree/master/tests/ShopBundle/Functional/Component/Redis/RedisFacadeTest.php) to your tests directory `tests/ShopBundle/Functional/Component/Redis/`

## [shopsys/coding-standards]
- We disallow using [Doctrine inheritance mapping](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/inheritance-mapping.html) in the Shopsys Framework
  because it causes problems during entity extension. Such problem with `OrderItem` was resolved during [making OrderItem extendable #715](https://github.com/shopsys/shopsys/pull/715)  
  If you want to use Doctrine inheritance mapping anyway, please skip `Shopsys\CodingStandards\Sniffs\ForbiddenDoctrineInheritanceSniff` ([#848](https://github.com/shopsys/shopsys/pull/848))

[Upgrade from v7.0.0 to Unreleased]: https://github.com/shopsys/shopsys/compare/v7.0.0...HEAD
[shopsys/framework]: https://github.com/shopsys/framework
[shopsys/coding-standards]: https://github.com/shopsys/coding-standards
