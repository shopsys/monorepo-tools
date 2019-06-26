# [Upgrade from v7.2.2 to Unreleased](https://github.com/shopsys/shopsys/compare/v7.2.2...HEAD)

This guide contains instructions to upgrade from version v7.2.2 to Unreleased.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]

### Infrastructure
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
- if you have `Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExporter` re-registered in your `services.yml`, add proper setter calls ([#1122](https://github.com/shopsys/shopsys/pull/1122))
    ```diff
        Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExporter
    +       calls:
    +           - method: setProgressBarFactory
    +           - method: setSqlLoggerFacade
    +           - method: setEntityManager
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
