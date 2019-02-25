# [Upgrade from v7.0.0-beta6 to Unreleased]

This guide contains instructions to upgrade from version v7.0.0-beta6 to Unreleased.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
### Application
- naming of promo code and discount code was unified ([#844](https://github.com/shopsys/shopsys/pull/844))
    - rename occurrences of `discount code` into `promo code` based on the changes from pull request in following files
        - `src/Shopsys/ShopBundle/Controller/Front/PromoCodeController.php`
        - `src/Shopsys/ShopBundle/Resources/scripts/frontend/promoCode.js`
        - `src/Shopsys/ShopBundle/Resources/views/Front/Content/Order/PromoCode/index.html.twig`
    - dump translations using `php phing dump-translations` and fill in the translations based on the changes from pull request
- check whether you extended class or method `ImageFacade::copyImages` or used it in your project and make sure it works like you intended ([#851](https://github.com/shopsys/shopsys/pull/851))
    - *(low priority)* remove `/var/www/html/var/cache` folder from your `@main_filesystem` filesystem storage if exists, set as local filesystem storage in path `%kernel.project_dir%` by default

### Tools
- *(low priority)* add `product-search-export-products` as a dependency of `build-demo` phing target in your `build.xml`
if you want to have products data exported to Elasticsearch after `build-demo` target is run ([#824](https://github.com/shopsys/shopsys/pull/824/files))

### Application
- *(low priority)* if you want to test behavior of cart with no listable product in it, implement functional test as it is in [#852](https://github.com/shopsys/shopsys/pull/852)
- copy all things related to data fixtures to your project, in particular:
    - copy `PerformanceDataCommand.php` into project namespace `ShopBundle/Command` from [`src/Shopsys/ShopBundle/Command`](https://github.com/shopsys/project-base/tree/master/src/Shopsys/ShopBundle/Command) of [shopsys/project-base] repository
    - add new configuration file to [`src/ShopBundle/Resources/config/services/commands.yml`](https://github.com/shopsys/project-base/tree/master/src/Shopsys/ShopBundle/Resources/config/services/commands.yml) and add Command configuration in it
    ```yaml
    services:
        _defaults:
            autowire: true
            autoconfigure: true
            public: false

        Shopsys\ShopBundle\Command\:
            resource: '../../../Command'
    ```
    - import new configuration file in `services.yml`
    ```diff
        imports:
            - { resource: forms.yml }
    +       - { resource: services/commands.yml }
    ```
    - if you used your own data fixtures or you have copied all our data fixtures and changed some of them, you don't need to do next steps
    - copy data fixtures into your project namespace `ShopBundle/DataFixtures` from [`src/Shopsys/ShopBundle/DataFixtures`](https://github.com/shopsys/project-base/tree/master/src/Shopsys/ShopBundle/DataFixtures) of [shopsys/project-base] repository
    - copy configuration file to `ShopBundle/Resources/config/services/data_fixtures.yml` from [`src/Shopsys/ShopBundle/Resources/config/services/data_fixtures.yml`](https://github.com/shopsys/project-base/tree/master/src/Shopsys/ShopBundle/Resources/config/services/data_fixtures.yml) of [shopsys/project-base] repository
    - import new configuration file in `services.yml`
    ```diff
        imports:
            - { resource: forms.yml }
            - { resource: services/commands.yml }
    +       - { resource: services/data_fixtures.yml }
    ```
    - change namespaces of data fixtures from `Shopsys/FrameworkBundle/DataFixtures` to `Shopsys/ShopBundle/DataFixtures` (in tests and *.yml configurations)
    - add skipping of 4 data fixture files to `easy-coding-standards.yml`
    ```diff
        skip:
            ObjectCalisthenics\Sniffs\Files\FunctionLengthSniff:
    +           - '*/src/Shopsys/ShopBundle/DataFixtures/*/*DataFixture.php'
    +           - '*/src/Shopsys/ShopBundle/DataFixtures/Demo/ProductDataFixtureLoader.php'

                //...

            ObjectCalisthenics\Sniffs\Files\ClassTraitAndInterfaceLengthSniff:
                - '*/tests/ShopBundle/Functional/Model/Product/ProductVisibilityRepositoryTest.php'
    +           - '*/src/Shopsys/ShopBundle/DataFixtures/Demo/MultidomainOrderDataFixture.phpFixture.php'
    +           - '*/src/Shopsys/ShopBundle/DataFixtures/Demo/OrderDataFixture.php'
    ```
    - if you extended some of data fixtures classes, you have to modify the class directly after you copied data fixtures from [shopsys/project-base]
    - you can follow [#854](https://github.com/shopsys/shopsys/pull/854) for inspiration

[Upgrade from v7.0.0-beta6 to Unreleased]: https://github.com/shopsys/shopsys/compare/v7.0.0-beta6...HEAD
[shopsys/framework]: https://github.com/shopsys/framework
[shopsys/project-base]: https://github.com/shopsys/project-base
