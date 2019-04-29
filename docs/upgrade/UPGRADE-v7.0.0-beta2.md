# [Upgrade from v7.0.0-beta1 to v7.0.0-beta2](https://github.com/shopsys/shopsys/compare/v7.0.0-beta1...v7.0.0-beta2)

This guide contains instructions to upgrade from version v7.0.0-beta1 to v7.0.0-beta2.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

*Note: instructions marked as "low priority" are not vital, however, we recommend to perform them as well during upgrading as it might ease your work in the future.*

## [shopsys/project-base]
- *(low priority)* [#497 adding php.ini to image is now done only in dockerfiles](https://github.com/shopsys/shopsys/pull/497)
    - you should make the same changes in your repository for the php.ini configuration files to be added to your Docker images
        - remove all volumes from `docker-compose.yml.dist` templates that include `php-ini-overrides.ini`
            - remove them also from your local `docker-compose.yml`
        - add `COPY php-ini-overrides.ini /usr/local/etc/php/php.ini` into `docker/php-fpm/Dockerfile`
    - from now on, you will have to rebuild your Docker images (`docker-compose up -d --build`) for the changes in the php.ini file to apply
- [#494 Microservices webserver using nginx + php-fpm](https://github.com/shopsys/shopsys/pull/494)
    - execute `docker-compose pull` to pull new microservice images and `docker-compose up -d` to start newly pulled microservices
    - url addresses to microservices have changed, you need to upgrade url address provided in `app/config/parameters.yml`
        - update parameter `microservice_product_search_url` from `microservice-product-search:8000` to `microservice-product-search`
        - update parameter `microservice_product_search_export_url`, from `microservice-product-search-export:8000` to `microservice-product-search-export`
- *(low priority)* when you upgrade `codeception/codeception` to version `2.5.0`, you have to change parameter `populate` to `true` in `tests/ShopBundle/Acceptance/acceptance.suite.yml`
- make changes in `composer.json`:
    - remove repositories:
        - `https://github.com/shopsys/doctrine2.git`
        - `https://github.com/shopsys/jparser.git`
        - `https://github.com/molaux/PostgreSearchBundle.git`
    - remove dependencies:
        - `"timwhitlock/jparser": "@dev"`
    - change dependencies:
        - `"doctrine/orm": "dev-doctrine-260-..."` -> `"shopsys/doctrine-orm": "2.6.2"`
        - `"intaro/postgres-search-bundle": "@dev"` -> `"shopsys/postgres-search-bundle": "0.1"`
- [#512 - Dockerfiles of microservices now use multi-stage build feature](https://github.com/shopsys/shopsys/pull/512)
    - increase `version` parameter of `docker-compose.yml` files to `3.4` and upgrade your `docker` and `docker-compose` binaries on your operating system (Win, Mac, Linux, ...) based on version section https://docs.docker.com/compose/compose-file/compose-versioning/#version-34
- [#513 - Manipulation with domains is modified and documented now](https://github.com/shopsys/shopsys/pull/513)
    - modify your `build.xml` according to this pull request
        - recalculations will be processed after `create-domains-data` command
        - creation of some database functions was moved from `create-domains-data` phing target to a new phing target `create-domains-db-functions`
    - modify your `build-dev.xml` according to this pull request
        - creation of some database functions was moved from `test-create-domains-data` phing target to a new phing target `test-create-domains-db-functions`
- *(low priority)* speed up composer in your `php-fpm` container by adding `RUN composer global require hirak/prestissimo` into `docker/php-fpm/Dockerfile`
- *(low priority)* to enable logging of errors in the `php-fpm` container, add `log_errors = true` to `docker/php-fpm/php-ini-overrides.ini`
- *(low priority)* make changes in `composer.json`:
    - remove conflict `"codeception/stub"`, the conflicting version doesn't exist anymore and conflict is solved
    - change conflict of `"symfony/dependency-injection"` to `"3.4.15|3.4.16"`

[shopsys/project-base]: https://github.com/shopsys/project-base
