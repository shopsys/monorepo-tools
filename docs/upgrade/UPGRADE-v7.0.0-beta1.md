# [Upgrade from v7.0.0-alpha6 to v7.0.0-beta1](https://github.com/shopsys/shopsys/compare/v7.0.0-alpha6...v7.0.0-beta1)

This guide contains instructions to upgrade from version v7.0.0-alpha6 to v7.0.0-beta1.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

*Note: instructions marked as "low priority" are not vital, however, we recommend to perform them as well during upgrading as it might ease your work in the future.*

## [shopsys/framework]
- *(low priority)* [#468 - Setting for docker on mac are now more optimized](https://github.com/shopsys/shopsys/pull/468)
    - if you use the Shopsys Framework with docker on the platform Mac, modify your
      [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-beta1/docker/conf/docker-compose-mac.yml.dist)
      and [`docker-sync.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-beta1/docker/conf/docker-sync.yml.dist) according to the new templates
    - next restart docker-compose and docker-sync
- *(low priority)* [#483 - updated info about Docker on Mac](https://github.com/shopsys/shopsys/pull/483)
    - if you use Docker for Mac and experience issues with `composer install` resulting in `Killed` status, try increasing the allowed memory
    - we recommend to set 2 GB RAM, 1 CPU and 2 GB Swap in `Docker -> Preferences… -> Advanced`
- we changed visibility of Controllers' and Factories' methods and properties to protected
    - you have to change visibility of overriden methods and properties to protected
    - you can use parents' methods and properties
- update `paths.yml`:
    - add `shopsys.data_fixtures_images.resources_dir: '%shopsys.data_fixtures.resources_dir%/images/'`
    - remove
      ```
        shopsys.demo_images_archive_url: https://images.shopsysdemo.com/demoImages.v11.zip
        shopsys.demo_images_sql_url: https://images.shopsysdemo.com/demoImagesSql.v8.sql
      ```
- remove phing target `img-demo` as demonstration images are part of data fixtures
    - remove `img-demo` phing target from `build.xml`
    - remove all occurrences of `img-demo` in `build-dev.xml`
    - remove all occurrences of `img-demo` from your build/deploy process

[shopsys/framework]: https://github.com/shopsys/framework
