# [Upgrade from v7.2.2 to Unreleased](https://github.com/shopsys/shopsys/compare/v7.2.2...HEAD)

This guide contains instructions to upgrade from version v7.2.2 to Unreleased.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]

### Infrastructure
- update your `docker/php-fpm/Dockerfile` production stage build ([#1177](https://github.com/shopsys/shopsys/pull/1177))
    ```diff
    RUN composer install --optimize-autoloader --no-interaction --no-progress --no-dev

    -RUN php phing composer-prod npm dirs-create assets
    +RUN php phing build-deploy-part-1-db-independent
    ```
- update Elasticsearch build configuration ([#1069](https://github.com/shopsys/shopsys/pull/1069))
    - copy new [Dockerfile from shopsys/project-base](https://github.com/shopsys/project-base/blob/master/docker/elasticsearch/Dockerfile)
    - update `docker-compose.yml` and `docker-compose.yml.dist`
        ```diff
            elasticsearch:
        -       image: docker.elastic.co/elasticsearch/elasticsearch-oss:6.3.2
        +       build:
        +           context: .
        +           dockerfile: docker/elasticsearch/Dockerfile
                container_name: shopsys-framework-elasticsearch
                ulimits:
                    nofile:
                        soft: 65536
                        hard: 65536
                ports:
                    - "9200:9200"
                volumes:
                    - elasticsearch-data:/usr/share/elasticsearch/data
                environment:
                    - discovery.type=single-node
        ```
    - if you deploy to the google cloud, copy new [`.ci/deploy-to-google-cloud.sh`](https://github.com/shopsys/project-base/blob/master/.ci/deploy-to-google-cloud.sh) script from `shopsys/project-base` ([#1126](https://github.com/shopsys/shopsys/pull/1126))

### Application
- **BC-BREAK** fix inconsistently named field `shortDescription` in Elasticsearch ([#1180](https://github.com/shopsys/shopsys/pull/1180))
    - in `ProductSearchExportRepositoryTest::getExpectedStructureForRepository()` (the test will fail otherwise)
        ```diff
        -   'shortDescription',
        +   'short_description',
        ```
    - in other places you might have used it in your custom code
- follow instructions in [the separate article](upgrade-instructions-for-read-model-for-product-lists.md) to introduce read model for frontend product lists into your project ([#1018](https://github.com/shopsys/shopsys/pull/1018))
    - we recommend to read [Introduction to Read Model](/docs/model/introduction-to-read-model.md) article
- copy a new functional test to avoid regression of issues with creating product variants in the future ([#1113](https://github.com/shopsys/shopsys/pull/1113))
    - you can copy-paste the class [`ProductVariantCreationTest.php`](https://github.com/shopsys/project-base/blob/master/tests/ShopBundle/Functional/Model/Product/ProductVariantCreationTest.php) into `tests/ShopBundle/Functional/Model/Product/` in your project
- prevent indexing `CustomerPassword:setNewPassword` by robots ([#1119](https://github.com/shopsys/shopsys/pull/1119))
    - add a `meta_robots` Twig block to your `@ShopsysShop/Front/Content/Registration/setNewPassword.html.twig` template:
        ```twig
        {% block meta_robots -%}
            <meta name="robots" content="noindex, follow">
        {% endblock %}
        ```
    - you should prevent indexing by robots using this block on all pages that are secured by an URL hash
- use `autocomplete="new-password"` attribute for password changing inputs to prevent filling it by browser ([#1121](https://github.com/shopsys/shopsys/pull/1121))
    - in `shopsys/project-base` repository this change was needed in 3 form classes (`NewPasswordFormType`, `UserFormType` and `RegistrationFormType`):
        ```diff
          'type' => PasswordType::class,
          'options' => [
        -     'attr' => ['autocomplete' => 'off'],
        +     'attr' => ['autocomplete' => 'new-password'],
          ],
        ```
- update your tests to use interfaces of factories fetched from dependency injection container
    -  update tests same way as in PR ([#970](https://github.com/shopsys/shopsys/pull/970/files))
- check your VAT calculations after it was modified in `shopsys/framework` ([#1129](https://github.com/shopsys/shopsys/pull/1129))
    - we strongly recommend seeing [the description of the PR](https://github.com/shopsys/shopsys/pull/1129) to understand the scope of this change
    - ẗo ensure you data is consistent, run DB migrations on your demo data and on a copy of production database
        - if you modified the price calculation or you altered the prices in the database directly, the migration might fail during a check sum - in that case the DB transaction will be reverted and it'll tell what to do
    - copy the functional test [OrderEditTest.php](https://github.com/shopsys/project-base/blob/master/tests/ShopBundle/Functional/Model/Order/OrderEditTest.php) into `tests/ShopBundle/Functional/Model/Order/` to test editing of order items
        - for the test to work, add test service definitions for `OrderItemDataFactory` and `OrderItemFactory` in your `src/Shopsys/ShopBundle/Resources/config/services_test.yml` configuration:
            ```diff

                 Shopsys\FrameworkBundle\Model\Customer\UserDataFactoryInterface: '@Shopsys\ShopBundle\Model\Customer\UserDataFactory'

            +    Shopsys\FrameworkBundle\Model\Order\Item\OrderItemDataFactoryInterface: '@Shopsys\ShopBundle\Model\Order\Item\OrderItemDataFactory'
            +
            +    Shopsys\FrameworkBundle\Model\Order\Item\OrderItemFactoryInterface: '@Shopsys\FrameworkBundle\Model\Order\Item\OrderItemFactory'
            +
                 Shopsys\FrameworkBundle\Model\Order\OrderDataFactoryInterface: '@Shopsys\ShopBundle\Model\Order\OrderDataFactory'

            ```
    - stop using the deprecated method `\Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation::getVatCoefficientByPercent()`, use `PriceCalculation::getVatAmountByPriceWithVat()` for VAT calculation instead
    - if you want to customize the VAT calculation (eg. revert it back to the previous implementation), extend the service `@Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation` and override the method `getVatAmountByPriceWithVat()`
    - if you created new tests regarding the price calculation they might start failing after the upgrade - in such case, please see the new VAT calculation and change the tests expectations accordingly
- use automatic wiring of Redis clients for easier checking and cleaning ([#1161](https://github.com/shopsys/shopsys/pull/1161))
    - if you have redefined the service `@Shopsys\FrameworkBundle\Component\Redis\RedisFacade` or `@Shopsys\FrameworkBundle\Command\CheckRedisCommand` in your project, or you instantiate the classes in your code:
        - instead of instantiating `RedisFacade` with an array of cache clients to be cleaned by `php phing redis-clean`, pass an array of all redis clients and another array of redis clients you don't want to clean (eg. `global` and `session`)
            ```diff
                Shopsys\FrameworkBundle\Component\Redis\RedisFacade:
                    arguments:
            -           - '@snc_redis.doctrine_metadata'
            -           - '@snc_redis.doctrine_query'
            -           - '@snc_redis.my_custom_cache'
            +           $allClients: !tagged snc_redis.client
            +           $persistentClients:
            +               - '@snc_redis.global'
            +               - '@snc_redis.session'
            ```
            - this allows you to use `!tagged snc_redis.client` in your DIC config for the first argument, ensuring that newly created clients will be registered by the facade
        - instead of instantiating `CheckRedisCommand` with an array of redis clients, pass an instance of `RedisFacade` instead
    - modify the functional test `\Tests\ShopBundle\Functional\Component\Redis\RedisFacadeTest` so it creates `RedisFacade` using the two arrays and add a new test case `testNotCleaningPersistentClient`
        - you can copy-paste the [`RedisFacadeTest`](https://github.com/shopsys/project-base/blob/master/tests/ShopBundle/Functional/Component/Redis/RedisFacadeTest.php) from `shopsys/project-base`
- implement `createFromIdAndName(int $id, string $name): FriendlyUrlData` method in your implementations of `FriendlyUrlDataFactoryInterface` as the method will be added to the interface in `v8.0.0` version ([#948](https://github.com/shopsys/shopsys/pull/948))
- use aliases of index and build version in index name in Elasticsearch for better usage when deploying ([#1133](https://github.com/shopsys/shopsys/pull/1133))
    - use method `ElasticsearchStructureManager::getCurrentIndexName` or `ElasticsearchStructureManager::getAliasName` instead of `ElasticsearchStructureManager::getIndexName` when calling a query to elasticSearch in order to always target the right index
        - use `getAliasName` if you need to access the index for read operations (eg. searching, filtering)
        - use `getCurrentIndexName` if need to write to the index or manipulate it (eg. product export)
    - run `php phing product-search-recreate-structure` to generate new indexes with aliases
    - use method `ElasticsearchStructureManager::deleteCurrentIndex` instead of `ElasticsearchStructureManager::deleteIndex` as it was deprecated
    - if you have extended `ElasticsearchStructureManager` in `services.yml` you'll need to send the `build-version` parameter to the 4th argument of the constructor or call `setBuildVersion` setter injector like this:
        ```diff
          Shopsys\FrameworkBundle\Component\Elasticsearch\ElasticsearchStructureManager:
              arguments:
                  - '%shopsys.elasticsearch.structure_dir%'
                  - '%env(ELASTIC_SEARCH_INDEX_PREFIX)%'
        +     calls:
        +         - method: setBuildVersion
        +           arguments:
        +               - '%build-version%'
        ```
    - copy a new functional test [`ElasticsearchStructureUpdateCheckerTest`](https://github.com/shopsys/project-base/blob/master/tests/ShopBundle/Functional/Component/Elasticsearch/ElasticsearchStructureUpdateCheckerTest.php) into `tests/ShopBundle/Functional/Component/Elasticsearch/` in your project
        - this test will ensure that the check whether to update Elasticsearch structure works as intended

### Configuration
- update `phpstan.neon` with following change to skip phpstan error ([#1086](https://github.com/shopsys/shopsys/pull/1086))
    ```diff
     #ignore annotations in generated code#
     -
    -    message: '#(PHPDoc tag @(param|return) has invalid value .+ expected TOKEN_IDENTIFIER at offset \d+)#'
    +    message: '#(PHPDoc tag @(param|return) has invalid value (.|\n)+ expected TOKEN_IDENTIFIER at offset \d+)#'
         path: %currentWorkingDirectory%/tests/ShopBundle/Test/Codeception/_generated/AcceptanceTesterActions.php
    ```
- change `name.keyword` field in Elasticsearch to sort each language properly ([#1069](https://github.com/shopsys/shopsys/pull/1069))
    - update field `name.keyword` to type `icu_collation_keyword` in `src/Shopsys/ShopBundle/Resources/definition/product/*.json` and set its `language` parameter according to what locale does your domain have:
        - example for English domain from [`1.json` of shopsys/project-base](https://github.com/shopsys/project-base/blob/master/src/Shopsys/ShopBundle/Resources/definition/product/1.json) repository.
            ```diff
                "name": {
                    "type": "text",
                    "analyzer": "stemming",
                    "fields": {
                        "keyword": {
            -               "type": "keyword"
            +               "type": "icu_collation_keyword",
            +               "language": "en",
            +               "index": false
                        }
                    }
                }
            ```
    - change `TestFlag` and `TestFlagBrand` tests in `FilterQueryTest.php` to assert IDs correctly:
        ```diff
            # TestFlag()
        -   $this->assertIdWithFilter($filter, [1, 5, 50, 16, 33, 39, 70, 40, 45]);
        +   $this->assertIdWithFilter($filter, [1, 5, 50, 16, 33, 70, 39, 40, 45]);

            # TestFlagBrand()
        -   $this->assertIdWithFilter($filter, [19, 17]);
        +   $this->assertIdWithFilter($filter, [17, 19]);
        ```
- extend DI configuration for your project by updating ([#1049](https://github.com/shopsys/shopsys/pull/1049))
    - `src/Shopsys/ShopBundle/Resources/config/services.yml`
        ```diff
        -    Shopsys\ShopBundle\Model\:
        -        resource: '../../Model/**/*{Facade,Factory,Repository}.php'
        +    Shopsys\ShopBundle\:
        +        resource: '../../**/*{Calculation,Facade,Factory,Generator,Handler,InlineEdit,Listener,Loader,Mapper,Parser,Provider,Recalculator,Registry,Repository,Resolver,Service,Scheduler,Subscriber,Transformer}.php'
        +        exclude: '../../{Command,Controller,DependencyInjection,Form,Migrations,Resources,Twig}'
        ```
    - `src/Shopsys/ShopBundle/Resources/config/services/twig.yml`
        ```diff
        -    Shopsys\ShopBundle\Twig\FlagsExtension: ~
        +    Shopsys\ShopBundle\Twig\:
        +        resource: '../../Twig/'
        ```
- unset the incompatible `excluded_404s` configuration from monolog handlers that don't use the `fingers_crossed` type ([#1154](https://github.com/shopsys/shopsys/pull/1154))
    - in `app/config/packages/dev/monolog.yml`:
        ```diff
            monolog:
               handlers:
                   main:
                       # change "fingers_crossed" handler to "group" that works as a passthrough to "nested"
                       type: group
                       members: [ nested ]
        +              excluded_404s: false
        ```
    - in `app/config/packages/test/monolog.yml`:
        ```diff
            monolog:
                handlers:
                    main:
                        type: "null"
        +               excluded_404s: false
        ```
- remove the useless route `front_category_panel` from your `routing_front.yml` ([#1042](https://github.com/shopsys/shopsys/pull/1042))
    - you'll find the configuration file in `src/Shopsys/ShopBundle/Resources/config/`

### Tools
- use the `build.xml` [Phing configuration](/docs/introduction/console-commands-for-application-management-phing-targets.md) from the `shopsys/framework` package ([#1068](https://github.com/shopsys/shopsys/pull/1068))
    - assuming your `build.xml` and `build-dev.xml` are the same as in `shopsys/project-base` in `v7.2.1`, just remove `build-dev.xml` and replace `build.xml` with this file:
        ```xml
        <?xml version="1.0" encoding="UTF-8"?>
        <project name="Shopsys Framework" default="list">

            <property file="${project.basedir}/build/build.local.properties"/>

            <property name="path.root" value="${project.basedir}"/>
            <property name="path.vendor" value="${path.root}/vendor"/>
            <property name="path.framework" value="${path.vendor}/shopsys/framework"/>

            <import file="${path.framework}/build.xml"/>

            <property name="is-multidomain" value="true"/>
            <property name="phpstan.level" value="0"/>

        </project>
        ```
    - if there are any changes in the your phing configuration, you'll need to make some customizations
        - read about [customization of phing targets and properties](/docs/introduction/console-commands-for-application-management-phing-targets.md#customization-of-phing-targets-and-properties) in the docs
        - if you have some own additional target definitions, copy them into your `build.xml`
        - if you have modified any targets, overwrite them in your `build.xml`
            - examine the target in the `shopsys/framework` package (either on [GitHub](/packages/framework/build.xml) or locally in `vendor/shopsys/framework/build.xml`)
            - it's possible that the current target's definition suits your needs now after the upgrade - you don't have to overwrite it if that's the case
            - for future upgradability of your project, it's better to use the original target via `shopsys_framework.TARGET_NAME` if that's possible (eg. if you want to execute a command before or after the original task)
            - if you think we can support your use case better via [phing target extensibility](/docs/contributing/guidelines-for-phing-targets.md#extensibility), please [open an issue](https://github.com/shopsys/shopsys/issues/new) or [create a pull request](/docs/contributing/guidelines-for-pull-request.md)
        - if you have deleted any targets, overwrite them in your `build.xml` with a fail task so it doesn't get executed by mistake:
            ```xml
            <target name="deleted-target" hidden="true">
                <fail message="Target 'deleted-target' is disabled on this project."/>
            </target>
            ```
    - if you modified the locales for extraction in `dump-translations`, you can now overwrite just a phing property `translations.dump.locales` instead of overwriting the whole target
        - for example, if you want to extract locales for German and English, add `<property name="translations.dump.locales" value="de en"/>` to your `build.xml`
    - some phing targets were marked as deprecated or were renamed, stop using them and use the new ones (the original targets will still work, but a warning message will be displayed):
        - `dump-translations` and `dump-translations-project-base` were deprecated, use `translations-dump` instead
        - `tests-static` was deprecated, use `tests-unit` instead
        - `test-db-check-schema` was deprecated, it is run automatically after DB migrations are executed
        - `build-demo-ci-diff` and `checks-ci-diff` were deprecated, use `build-demo-ci` and `checks-ci` instead
        - `composer` was deprecated, use `composer-prod` instead
        - `generate-build-version` was deprecated, use `build-version-generate` instead
        - `(test-)create-domains-data` was deprecated, use `(test-)domains-data-create` instead
        - `(test-)create-domains-db-functions` was deprecated, use `(test-)domains-db-functions-create` instead
        - `(test-)generate-friendly-urls` was deprecated, use `(test-)friendly-urls-generate` instead
        - `(test-)replace-domains-urls` was deprecated, use `(test-)domains-urls-replace` instead
        - `(test-)load-plugin-demo-data` was deprecated, use `(test-)plugin-demo-data-load` instead
        - don't forget to update your Dockerfiles, Kubernetes manifests, scripts and other files that might reference the phing targets above
- we recommend upgrading PHPStan to level 4 [#1040](https://github.com/shopsys/shopsys/pull/1040)
    - you'll find detailed instructions in separate article [Upgrade Instructions for Upgrading PHPStan to Level 4](/docs/upgrade/phpstan-level-4.md)

[shopsys/framework]: https://github.com/shopsys/framework
