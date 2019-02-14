# [Upgrade from v7.0.0-beta2 to v7.0.0-beta3](https://github.com/shopsys/shopsys/compare/v7.0.0-beta2...v7.0.0-beta3)

This guide contains instructions to upgrade from version v7.0.0-beta2 to v7.0.0-beta3.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
### Infrastructure
- replace your `docker/php-fpm/Dockerfile` file with [version from GitHub](https://github.com/shopsys/shopsys/blob/v7.0.0-beta3/project-base/docker/php-fpm/Dockerfile), with following changes
    - main php-fpm container now uses multi-stage build feature ([#533](https://github.com/shopsys/shopsys/pull/533))
    - github token is removed ([#551](https://github.com/shopsys/shopsys/pull/551))
    - development docker build target is before production and CI targets ([#566](https://github.com/shopsys/shopsys/pull/566))
    - php-fpm image has standard working directory (`/var/www/html`) in CI stage ([#557](https://github.com/shopsys/shopsys/pull/557))
    - standards check are now running in CI build process ([#558](https://github.com/shopsys/shopsys/pull/558))
    - `www_data_uid` and `www_data_gid` arguments are optional using an if condition (for building ci and production stage) ([#566](https://github.com/shopsys/shopsys/pull/566))
    - in a `ci` stage is a command to change the environment to keep building in `prod` environment ([#543](https://github.com/shopsys/shopsys/pull/543/files#diff-50a0e02c146dc64c2a172b42022589fa))
- replace your `docker/php-fpm/docker-php-entrypoint` file with [version from GitHub]((https://github.com/shopsys/shopsys/blob/v7.0.0-beta3/project-base/docker/php-fpm/docker-php-entrypoint))
- update `docker/conf/docker-compose-prod.yml` file
    - add new smtp container ([#530](https://github.com/shopsys/shopsys/pull/530))
        ```yaml
        smtp-server:
            image: namshi/smtp:latest
            container_name: shopsys-framework-smtp-server
            networks:
                - shopsys-network
        ```
    - remove smtp row from `extra_hosts` ([#530](https://github.com/shopsys/shopsys/pull/530))
    - check whether there exists `depends_on` property between webserver and php-fpm services since this is needed for functional volume mounting (see [#598](https://github.com/shopsys/shopsys/pull/598))
- update `docker-compose.yml` file
    - update php-fpm build configuration ([#533](https://github.com/shopsys/shopsys/pull/533))
        - change `context` to current directory (`.`)
        - add `dockerfile` directive with value `docker/php-fpm/Dockerfile`
        - add `target` environment (`development`)
    - *(MacOS only)* remove these lines ([#503](https://github.com/shopsys/shopsys/pull/503/))
        ```yaml
        - shopsys-framework-elasticsearch-data-sync:/usr/share/elasticsearch/data
        shopsys-framework-postgres-data-sync:
            external: true
        shopsys-framework-elasticsearch-data-sync:
            external: true
        ```
    - *(low priority)* remove all `links` because they are unnecessary ([#528](https://github.com/shopsys/shopsys/pull/528))s
    - *(low priority)* remove github token ([#551](https://github.com/shopsys/shopsys/pull/551))
- *(MacOS only)* remove these lines from `docker-sync.yml` ([#503](https://github.com/shopsys/shopsys/pull/503/))
    ```yaml
    shopsys-framework-postgres-data-sync:
        src: './project-base/var/postgres-data/'
        host_disk_mount_mode: 'cached'
    shopsys-framework-elasticsearch-data-sync:
        src: './project-base/var/elasticsearch-data/'
        host_disk_mount_mode: 'cached'
    ```
- in file `docker/nginx/nginx.conf`, change parameter `fastcgi_param HTTPS` to `$http_x_forwarded_proto;` so the protocol of the original HTTP request is passed into php-fpm container ([#530](https://github.com/shopsys/shopsys/pull/530))
- in file `docker/nginx/nginx.conf`, change location for images from `^/content/images/` to `^/content(-test)?/images/` ([#547](https://github.com/shopsys/shopsys/pull/547))
- *(low priority)* update `kubernetes/deployments/webserver-php-fpm.yml` to simplify the CI build ([#557](https://github.com/shopsys/shopsys/pull/557/files#diff-7e9545dc0b15fe031affe39181e97969))
    - change `command` to `["sh", "-c", "cp -r /var/www/html/. /tmp/source-codes"]`
    - change `mountPath` to `/tmp/source-codes`
- *(low priority)* make docker ignore some files to speed up build ([#535](https://github.com/shopsys/shopsys/pull/535))
    - add `.dockerignore` file with following content
        ```
        # ignore .git (can be volume-mounted in development environment)
        .git

        # ignore directories with meta-data
        .idea
        nbproject

        # ignore directories that should be created and filled during the image build
        node_modules
        var
        !var/.gitkeep
        web/assets/scripts
        web/bundles
        web/components
        web/content
        vendor

        # ignore kubernetes manifests
        kubernetes

        # ignore docker configs for other images than php-fpm, and the php-fpm's Dockerfile itself
        docker
        !docker/php-fpm
        docker/php-fpm/Dockerfile
        ```
    - *(MacOS, Windows only)* add following directories as ignored in `docker-sync.yml`
        ```yaml
        'docs',
        'kubernetes',
        'nbproject',
        ```

### Tools
- Phing
    - `build-dev.xml`
        - change `test-db-fixtures-demo-singledomain` command to `<arg value="doctrine:fixtures:load" />` and remove `--fixtures` argument
            because command `shopsys:fixtures:load` doesn't exist anymore ([#568](https://github.com/shopsys/shopsys/pull/568/files#diff-ae23427cd8e4dae17850f08f56308c3f))
        - rename `test-db-fixtures-demo-singledomain` to `test-db-fixtures-demo` ([#568](https://github.com/shopsys/shopsys/pull/568/files#diff-ae23427cd8e4dae17850f08f56308c3f))
        - add following arguments to `ecs`, `ecs-fix` and `ecs-diff` targets to check and fix coding standards in documentation files ([#580](https://github.com/shopsys/shopsys/pull/580/files#diff-ae23427cd8e4dae17850f08f56308c3f))
            ```xml
            <arg path="${path.root}/*.md" />
            <arg path="${path.root}/docs" />
            ```
        - make your `test-create-domains-data` task dependent on `test-create-domains-db-functions` task ([#538](https://github.com/shopsys/shopsys/pull/538/files#diff-ae23427cd8e4dae17850f08f56308c3f))
        - switch dependency order of `test-db-demo` to `...,test-create-domains-data,test-db-fixtures-demo,...`
        - remove `test-db-fixtures-demo-multidomain` and its usage ([#568](https://github.com/shopsys/shopsys/pull/568/files#diff-ae23427cd8e4dae17850f08f56308c3f))
        - add new target `test-dirs-create` and add it as a dependency after each `dirs-create` in this file ([#547](https://github.com/shopsys/shopsys/pull/547))
            ```xml
            <target name="test-dirs-create" description="Creates application directories for content, images, uploaded files, etc. for test environment">
                <exec executable="${path.php.executable}" passthru="true" checkreturn="true">
                    <arg value="${path.bin-console}" />
                    <arg value="--env=test" />
                    <arg value="shopsys:create-directories" />
                </exec>
            </target>
            ```
        - modify file according to [#545](https://github.com/shopsys/shopsys/pull/545) to speed up deployment process of built docker images of php-fpm
            - remove `composer-dev`, `npm`, `assets` and `tests-acceptance-build` dependencies from `build-demo-ci` and `build-demo-ci-diff` targets
            - replace `standards,test` dependencies with `test-db-demo,tests-functional,tests-smoke` in `checks-ci` target
            - replace `standards-diff,test` dependencies with `test-db-demo,tests-functional,tests-smoke` in `checks-ci-diff` target
            - replace `tests` target with following two
                ```xml
                 <target name="tests-static" depends="tests-unit" description="Runs unit tests."/>
                 <target name="tests" depends="test-db-demo,tests-static,tests-functional,tests-smoke" description="Runs unit, functional and smoke tests on a newly built test database."/>
                ```
                *Note: `tests-functional` phing target was renamed from `tests-database` within `(low priority) rename Database tests to Functional tests` instruction*
    - `build.xml`
        - change `db-fixtures-demo-singledomain` command to `<arg value="doctrine:fixtures:load" />` and remove `--fixtures` argument
            because command `shopsys:fixtures:load` doesn't exist anymore ([#568](https://github.com/shopsys/shopsys/pull/568/files#diff-ae23427cd8e4dae17850f08f56308c3f))
        - rename `db-fixtures-demo-singledomain` to `db-fixtures-demo` ([#568](https://github.com/shopsys/shopsys/pull/568/files#diff-e22ff16d006c03464bffffa8462c123a))
        - make your `create-domains-data` task dependent on `create-domains-db-functions` task ([#538](https://github.com/shopsys/shopsys/pull/538/files#diff-ae23427cd8e4dae17850f08f56308c3f))
        - switch dependency order of `db-demo` to `...,create-domains-data,db-fixtures-demo,...`
        - remove `db-fixtures-demo-multidomain` and its usage ([#568](https://github.com/shopsys/shopsys/pull/568/files#diff-ae23427cd8e4dae17850f08f56308c3f))
        - add to target `wipe-excluding-logs` directory `content-test` to be truncated too ([#547](https://github.com/shopsys/shopsys/pull/547))
            ```xml
            <fileset dir="${path.web}/content-test/">
                <exclude name="/" />
            </fileset>
            ```
- *(low priority)* use auto-configuration of domain URLs with composer ([#540](https://github.com/shopsys/shopsys/pull/540))
    - to simplify installation, add `"Shopsys\\FrameworkBundle\\Command\\ComposerScriptHandler::postInstall"` to `post-install-cmd` and `"Shopsys\\FrameworkBundle\\Command\\ComposerScriptHandler::postUpdate"` to  `post-update-cmd` scripts in your `composer.json`
    - you will not have to copy the `domains_urls.yml.dist` during the installation anymore
    - you can also remove the now redundant Phing target `domains-urls-check` from your `build.xml` and `build-dev.xml`
- *(low priority)* remove unsupported `syntaxCheck` attribute from your `phpunit.xml` configuration file ([#592](https://github.com/shopsys/shopsys/pull/592))

### Configuration
- use `content-test` directory instead of content during the tests ([#547](https://github.com/shopsys/shopsys/pull/547))
    - add following parameter into `parameters_test.yml.dist` and `parameters_test.yml`
        ```yaml
        shopsys.content_dir_name: 'content-test'
        ```
    - add following parameter into `paths.yml`
        ```yaml
        shopsys.content_dir_name: 'content'
        ```
    - replace all occurrences of `/content/` with new parameter `/%shopsys.content_dir_name%/` in `routing-front.yml` and `paths.yml` files

### Database migrations
- after running database migrations, all your products will be using manual pricing and will have set prices for all pricing groups in a fashion that will keep the final price as same as before
    - we strongly recommend to review [`Version20181114134959`](https://github.com/shopsys/shopsys/blob/v7.0.0-beta3/packages/framework/src/Migrations/Version20181114134959.php)
    and [`Version20181114145250`](https://github.com/shopsys/shopsys/blob/v7.0.0-beta3/packages/framework/src/Migrations/Version20181114145250.php) migrations before executing them on your real data,
    especially if there were any modifications in your product pricing implementation on the project. ([#595](https://github.com/shopsys/shopsys/pull/595))

### Application
- remove dependencies on automatic product price calculation and pricing group coefficients ([#595](https://github.com/shopsys/shopsys/pull/595))
    - `ProductPriceCalculation::calculatePrice()` is still available, however, it always uses the manual price calculation
    - following (public and protected) constants, properties and methods are not available anymore:
        - `Currency::getReversedExchangeRate()`
        - `CurrencyFacade::getDomainConfigsByCurrency()`
        - `PricingGroup::$coefficient`
        - `PricingGroup::getCoefficient()`
        - `PricingGroupData::$coefficient`
        - `Product::PRICE_CALCULATION_TYPE_AUTO`
        - `Product::PRICE_CALCULATION_TYPE_MANUAL`
        - `Product::$price`
        - `Product::$priceCalculationType`
        - `Product::setPrice()`
        - `Product::getPrice()`
        - `Product::getPriceCalculationType()`
        - `ProductData::$priceCalculationType`
        - `ProductData::$price`
        - `ProductDataFixtureLoader::COLUMN_MAIN_PRICE`
        - `ProductDataFixtureLoader::COLUMN_PRICE_CALCULATION_TYPE`
        - `ProductFormType::VALIDATION_GROUP_AUTO_PRICE_CALCULATION`
        - `ProductFormType::VALIDATION_GROUP_MANUAL_PRICE_CALCULATION`
        - `ProductInputPriceFacade::getInputPrice()`
        - `ProductManualInputPriceFacade::getAllByProduct()`
        - `ProductManualInputPriceFacade::deleteByProduct()`
        - `ProductManualInputPriceRepository::getByProductAndDomainConfigs()`
        - `ProductService::setInputPrice()`
    - interfaces of following (public and protected) methods have changed:
        - `InputPriceRecalculator::__construct()`
        - `ProductDataFactory::__construct()`
        - `ProductCalculatedPricesType::_construct()`
        - `ProductPriceCalculation::__construct()`
    - following classes have been removed:
        - `AdminProductPriceCalculationFacade`
        - `InvalidPriceCalculationTypeException`
        - `ProductBasePriceCalculationException`
        - `ProductInputPriceService`
    - due to the removal of `Product::$price` and `PricingGroup::$coefficient` you have to fix your tests
        - we cannot provide exact instruction for fixing tests as we don't know what do you test
        - please find hints in tests that we fixed during [#595](https://github.com/shopsys/shopsys/pull/595)
- remove all usages of `\Shopsys\FrameworkBundle\Command\LoadDataFixturesCommand` and `\Shopsys\FrameworkBundle\Component\DataFixture\FixturesLoader` as we no longer support data fixtures in multiple directories ([#568](https://github.com/shopsys/shopsys/pull/568))
    - use native doctrine fixtures  
- we moved multidomain data fixtures in namespace `\Shopsys\FrameworkBundle\DataFixtures\DemoMultidomain` to `\Shopsys\FrameworkBundle\DataFixtures\Demo` ([#568](https://github.com/shopsys/shopsys/pull/568))
    - check for their usage in your code and change the namespace appropriately
- change calling of `\Shopsys\FrameworkBundle\DataFixtures\ProductDataFixtureReferenceInjector::loadReferences` ([#568](https://github.com/shopsys/shopsys/pull/568))
    - the last parameter is no longer `bool`, but `integer` - domain ID
- `Shopsys\FrameworkBundle\Model\Product\ProductFacade::create()` and `Shopsys\FrameworkBundle\Model\Product\ProductFactory` were modified
    - if you extended the classes in your project, please check out the changes in the framework ones (and the reasons for the changes) in [#581](https://github.com/shopsys/shopsys/pull/581/files)
- register templates for new FormTypes (DisplayOnlyCustomerType and OrderItemsType) by adding following lines to `app/config/packages/twig.yml` ([#576](https://github.com/shopsys/shopsys/pull/576))
    ```yaml
    - '@ShopsysFramework/Admin/Form/orderItems.html.twig'
    - '@ShopsysFramework/Admin/Form/displayOnlyCustomer.html.twig'
    ```
- if you have extended OrderFormType template (`@ShopsysFramework/Admin/Content/Order/edit.html.twig`) to add new field or group, you have to create OrderFormTypeExtension instead (see [example on demoshop](https://github.com/shopsys/demoshop/pull/27/commits/c6a54c6592ebab8cba6d86e47985fb31f511ba30#diff-0c583c8b886a9844ffd793f1afa83ec9)) ([#576](https://github.com/shopsys/shopsys/pull/576))
- DisplayOnlyUrlType has been redesigned to support all possible routes ([#576](https://github.com/shopsys/shopsys/pull/576))
    - it now has 1 required (`route`) and 3 optional (`route_params, route_label and domain_id`) parameters
    - example of usage:
        ```php
        $variantGroup->add('mainVariantUrl', DisplayOnlyUrlType::class, [
            'label' => t('Product is variant'),
            'route' => 'admin_product_edit',
            'route_params' => [
                'id' => $product->getMainVariant()->getId(),
            ],
            'route_label' => $product->getMainVariant()->getName(),
        ]);
        ```
- *(low priority)* rename Database tests to Functional tests
    - rename base class `DatabaseTestCase` and its usages to `TransactionFunctionalTestCase`
    - rename test namespace `Database` to `Functional`
    - rename phing target `tests-db` to `tests-functional` and value of `--testsuite` from `Database` to `Functional`
    - change namespaces in Javascript compiler testing data, if you're using them from project-base, specifically in
        - `testClassName.expected.js`
        - `testClassName.js`
        - `testDefinedConstant.expected.js`
        - `testDefinedConstant.js`
        - `testUndefinedConstant.js`
    - you can follow [#541 Rename database tests to functional tests](https://github.com/shopsys/shopsys/pull/541) or [#27 Upgrade demoshop to beta4 version](https://github.com/shopsys/demoshop/pull/27/commits/b0c404e73ea3f4210cfb71faeb94720fe71d72b7#diff-63d5b035284107155c7becd538ca5009)
- *(low priority)* you can change your `Shopsys\Environment` class for consistent env setting during `composer install` ([see diff](https://github.com/shopsys/project-base/commit/eedcf2ea9eaaef6c4f53a83fedbbd3c34428af83))
    - in `docker/php-fpm/Dockerfile` in a `ci` stage should be a command to change the environment to keep building in `prod` environment, otherwise its built in `dev` ([see diff](https://github.com/shopsys/project-base/commit/c974597237992b3083ed48f0937715de9cf5981d))
- check correctness of translation and validation constants in *.po files for passing automation tests
    - these should exist
        ```
        Please choose a valid combination of transport and payment
        ```

## [shopsys/coding-standards]
- there are a few new standards, i.e. [new fixers enabled](https://github.com/shopsys/shopsys/pull/573/files#diff-709e8469a9fc8c8b45f8b285ac1a4c92) in the `easy-coding-standard.yml` config that enforce using annotations for all your methods:
    - if you want to use the standards as well, let the fixers check and fix your code
        - on projects built from `shopsys/project-base` just run `php phing standards-fix`
        - on any other projects, where you do not have our phing targets available, run `vendor/bin/ecs check path/to/your/source-codes --fix`
    - if you do not want to use these standards, disable them in your custom configuration (`easy-coding-standard.yml` by default)

[shopsys/framework]: https://github.com/shopsys/framework
[shopsys/coding-standards]: https://github.com/shopsys/coding-standards
