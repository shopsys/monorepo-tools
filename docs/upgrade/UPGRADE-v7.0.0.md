# [Upgrade from v7.0.0-beta6 to v7.0.0]

This guide contains instructions to upgrade from version v7.0.0-beta6 to v7.0.0.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]

### Tools
- *(low priority)* add `product-search-export-products` as a dependency of `build-demo` phing target in your `build.xml`
if you want to have products data exported to Elasticsearch after `build-demo` target is run ([#824](https://github.com/shopsys/shopsys/pull/824/files))

### Application
- naming of promo code and discount code was unified ([#844](https://github.com/shopsys/shopsys/pull/844))
    - rename occurrences of `discount code` into `promo code` based on the changes from pull request in following files
        - `src/Shopsys/ShopBundle/Controller/Front/PromoCodeController.php`
        - `src/Shopsys/ShopBundle/Resources/scripts/frontend/promoCode.js`
        - `src/Shopsys/ShopBundle/Resources/views/Front/Content/Order/PromoCode/index.html.twig`
    - dump translations using `php phing dump-translations` and fill in the translations based on the changes from pull request
- check whether you extended class or method `ImageFacade::copyImages` or used it in your project and make sure it works like you intended ([#851](https://github.com/shopsys/shopsys/pull/851))
    - *(low priority)* remove `/var/www/html/var/cache` folder from your `@main_filesystem` filesystem storage if exists, set as local filesystem storage in path `%kernel.project_dir%` by default
- use `Money` class for representing monetary values in the whole application ([#821](https://github.com/shopsys/shopsys/pull/821))
    - we recommend first reading the article [How to Work with Money](/docs/introduction/how-to-work-with-money.md) which explains the concept
    - you can take a look at [what was modified in `shopsys/project-base` during this change](https://github.com/shopsys/project-base/compare/cb6d02f335819aeff575dec01bda5b228263a2eb...c08cac7b55ebc46b43c2e988d36e2f122cbb4598#files_bucket) (24 files)
    - you'll find detailed instructions in separate article [Upgrade Instructions for Money Class](/docs/upgrade/money-class.md)
- you need to provide `$temporaryFilenames` parameter anywhere you use `ImageFactoryInterface::create()` and `ImageFacade::uploadImage()` functions ([#869](https://github.com/shopsys/shopsys/pull/869))
    - the parameter is not nullable now
- if you're using protected method `ImageProcessor::removeFileIfRenamed()` in your code, remove the usage due to the method was removed and the code was moved to `convertToShopFormatAndGetNewFilename()` ([#869](https://github.com/shopsys/shopsys/pull/869))
- copy all things related to data fixtures to your project, in particular:
    - copy `PerformanceDataCommand.php` into project namespace `ShopBundle/Command` from [`src/Shopsys/ShopBundle/Command`](https://github.com/shopsys/project-base/tree/v7.0.0/src/Shopsys/ShopBundle/Command) of [shopsys/project-base] repository
    - add new configuration file to [`src/ShopBundle/Resources/config/services/commands.yml`](https://github.com/shopsys/project-base/tree/v7.0.0/src/Shopsys/ShopBundle/Resources/config/services/commands.yml) and add Command configuration in it
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
    - copy data fixtures into your project namespace `ShopBundle/DataFixtures` from [`src/Shopsys/ShopBundle/DataFixtures`](https://github.com/shopsys/project-base/tree/v7.0.0/src/Shopsys/ShopBundle/DataFixtures) of [shopsys/project-base] repository
    - copy configuration file to `ShopBundle/Resources/config/services/data_fixtures.yml` from [`src/Shopsys/ShopBundle/Resources/config/services/data_fixtures.yml`](https://github.com/shopsys/project-base/tree/v7.0.0/src/Shopsys/ShopBundle/Resources/config/services/data_fixtures.yml) of [shopsys/project-base] repository
    - import new configuration file in `services.yml`
    ```diff
        imports:
            - { resource: forms.yml }
            - { resource: services/commands.yml }
    +       - { resource: services/data_fixtures.yml }
    ```
    - change namespaces of data fixtures from `Shopsys\FrameworkBundle\DataFixtures` to `Shopsys\ShopBundle\DataFixtures` (in tests and *.yml configurations)
        - check correctness of registered DataFixture services based on the state of [services.yml](https://github.com/shopsys/project-base/blob/v7.0.0/src/Shopsys/ShopBundle/Resources/config/services.yml) of [shopsys/project-base] repository
    - change the value of data fixtures resource folder in `app/config/paths.yml`
        ```diff
        -    shopsys.data_fixtures.resources_dir: '%shopsys.framework.root_dir%/src/DataFixtures/resources'
        +    shopsys.data_fixtures.resources_dir: '%shopsys.root_dir%/src/Shopsys/ShopBundle/DataFixtures/resources'
        ```
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
- *(low priority)* if you want to test behavior of cart with no listable product in it, implement functional test as it is in [#852](https://github.com/shopsys/shopsys/pull/852)
- *(low priority)* to implement default image sizes for individual devices width ([#836](https://github.com/shopsys/shopsys/pull/836)), you have to
    - update `src/Shopsys/ShopBundle/Resources/config/images.yml` as has been changed in this [commit](https://github.com/shopsys/shopsys/pull/836/files#diff-6519f98eb70e3d78e0f9756083222ff3)
    - update all image macros in twig template `src/Shopsys/ShopBundle/Resources/views/Front/Content/Advert/box.html.twig` as shown below
    ```twig
    {{ image(advert, { size: advert.positionName }) }}
    ```
    - in order to show changes properly for section `noticer` in `images.yml` you need to create banner in administration as **Image with link**.
    - remove all images in folders (if a folder with images exists) in order to generate new images
        - `web/content/images/product/default`
        - `web/content/images/product/list`
        - `web/content/images/sliderItem/default`

[Upgrade from v7.0.0-beta6 to v7.0.0]: https://github.com/shopsys/shopsys/compare/v7.0.0-beta6...v7.0.0
[shopsys/framework]: https://github.com/shopsys/framework
[shopsys/project-base]: https://github.com/shopsys/project-base
