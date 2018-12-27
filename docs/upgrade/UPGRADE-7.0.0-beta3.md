## [From 7.0.0-beta2 to v7.0.0-beta3](https://github.com/shopsys/shopsys/compare/v7.0.0-beta2...v7.0.0-beta3)

### [shopsys/framework]
- [#595 automatic product price calculation has been removed along with pricing group coefficients](https://github.com/shopsys/shopsys/pull/595)
    - after running database migrations, all your products will be using manual pricing and will have set prices for all pricing groups in a fashion that will keep the final price as same as before
        - we strongly recommend to review `Version20181114134959` and `Version20181114145250` migrations before executing them on your real data, especially if there were any modifications in your product pricing implementation on the project.
        If any of the migrations does not suit you, there is an option to skip it, see [our Database Migrations docs](https://github.com/shopsys/shopsys/blob/master/docs/introduction/database-migrations.md#reordering-and-skipping-migrations)
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
        - please find hints in tests that we fixed during [#595](https://github.com/shopsys/shopsys/pull/595/files)
- remove all usages of `\Shopsys\FrameworkBundle\Command\LoadDataFixturesCommand` and `\Shopsys\FrameworkBundle\Component\DataFixture\FixturesLoader` as we no longer support data fixtures in multiple directories
- we moved multidomain data fixtures in namespace `\Shopsys\FrameworkBundle\DataFixtures\DemoMultidomain` to `\Shopsys\FrameworkBundle\DataFixtures\Demo`
    - check for their usage in your code and change the namespace appropriately
- change calling of `\Shopsys\FrameworkBundle\DataFixtures\ProductDataFixtureReferenceInjector::loadReferences`
    - the last parameter is no longer `bool`, but `integer` - domain ID

### [shopsys/project-base]
- *(optional)* [#592 phpunit: remove unsupported syntaxCheck attribute](https://github.com/shopsys/shopsys/pull/592)
    - remove unsupported `syntaxCheck` attribute from your `phpunit.xml` configuration file
- `Shopsys\FrameworkBundle\Model\Product\ProductFacade::create()` and `Shopsys\FrameworkBundle\Model\Product\ProductFactory` were modified
    - if you extended the classes in your project, please check out the changes in the framework ones (and the reasons for the changes) in [the pull request](https://github.com/shopsys/shopsys/pull/581/files)
- [#576 OrderFormType in administration is now rendered by default](https://github.com/shopsys/shopsys/pull/576)
    - DisplayOnlyUrlType has been redesigned to support all possible routes, it now has 1 required (`route`) and 3 optional (`route_params, route_label and domain_id`) parameters
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
    - if you have extended OrderFormType or its template in any way you will need to review your changes and update them appropriately
    - there are two new FormTypes DisplayOnlyCustomerType and OrderItemsType.
        - you need to register their templates in `app/config/packages/twig.yml`
- *(optional)* [#540 domains URLs are auto-configured during "composer install"](https://github.com/shopsys/shopsys/pull/540)
    - to simplify installation, add `"Shopsys\\FrameworkBundle\\Command\\ComposerScriptHandler::postInstall"` to `post-install-cmd` and `"Shopsys\\FrameworkBundle\\Command\\ComposerScriptHandler::postUpdate"` to  `post-update-cmd` scripts in your `composer.json`
    - you will not have to copy the `domains_urls.yml.dist` during the installation anymore
    - you can also remove the now redundant Phing target `domains-urls-check` from your `build.xml` and `build-dev.xml`
- *(optional)* [#428 Removed depends_on and links from docker-compose.yml files](https://github.com/shopsys/shopsys/pull/528)
    - remove all `depends_on` and `links` from your docker-compose files because they are unnecessary
    - the only exception is the `webserver` container that should depend on `php-fpm` in the production configuration `docker-compose.prod.yml.dist`, otherwise a volume will not mount properly (see [PR #598](https://github.com/shopsys/shopsys/pull/598))
- [#538 - phing targets: create-domains-data is now dependent on create-domains-db-functions](https://github.com/shopsys/shopsys/pull/538)
    - in your `build.xml`, make your `create-domains-data` task dependent on `create-domains-db-functions` task
    - in your `build-dev.xml`, make your `test-create-domains-data` task dependent on `test-create-domains-db-functions` task
- [#558 - Missing standards check in CI build process #558](https://github.com/shopsys/shopsys/pull/558)
    - the Dockerfile for `php-fpm` has changed for CI stage build, update your `docker/php-fpm/Dockerfile`
- *(optional)* [#535 added .dockerignore files](https://github.com/shopsys/shopsys/pull/535)
    - to make your Docker image build faster, copy the `.dockerignore` file to the root of you project
    - if you're using Docker-sync, add the directories mentioned in the PR into `sync_exclude` section of your `docker-sync.yml` to make the synchronization faster as well
- *(optional)* [#557 - php-fpm image has standard workdir (/var/www/html) in ci stage](https://github.com/shopsys/shopsys/pull/557)
    - update your `docker/php-fpm/Dockerfile` and `kubernetes/deployments/webserver-php-fpm.yml` according to [the pull request](https://github.com/shopsys/shopsys/pull/557) to simplify the CI build
- [#580 Removed trailing whitespaces from markdown files ](https://github.com/shopsys/shopsys/pull/580)
    - remove these lines from `.dockerignore`
        ```
        # ignore the docs (along with .md files in root dir)
        *.md
        docs
        ```
    - run `docker-compose down` to turn off your containers
    - *(MacOS, Windows only)*
        - run `docker-sync clean` so your volumes will be removed
        - remove excluding of `docs` folder from `docker-sync.yml`
        - run `docker-sync start` to create volumes
    - run `docker-compose up -d --build --force-recreate` to start application
    - phing target for checking and fixing standards has changed, update `build-dev.xml` according to the changes
- Make couple of changes in phing targets
    - remove `db-fixtures-demo-multidomain` and `test-db-fixtures-demo-multidomain` and their usages
    - switch dependency order of `db-demo` to `...,create-domains-data,db-fixtures-demo-singledomain,...`
    - switch dependency order of `test-db-demo` to `...,test-create-domains-data,test-db-fixtures-demo-singledomain,...`
    - change `db-fixtures-demo-singledomain` and `test-db-fixtures-demo-singledomain` command to `<arg value="doctrine:fixtures:load" />`
      as command `shopsys:fixtures:load` doesn't exist anymore and remove `--fixtures` argument
    - rename `db-fixtures-demo-singledomain` to `db-fixtures-demo`
    - rename `test-db-fixtures-demo-singledomain` to `test-db-fixtures-demo`
- *(optional)* [#566 - Set development docker build target before production and CI targets](https://github.com/shopsys/shopsys/pull/566)
    - move `development` stage build before `production` stage in `docker/php-fpm/Dockerfile` to make your dev build faster
    - make the `www_data_uid` and `www_data_gid` arguments optional using an if condition (for building ci and production stage)
- *(optional)* [#551 - github token erase](https://github.com/shopsys/shopsys/pull/551)
    - remove the lines mentioning `github_oauth_token` from your `docker/php-fpm/Dockerfile` and `docker-compose.yml`
    - rebuild `php-fpm` container
- *(optional)* you can change your `Shopsys\Environment` class for consistent env setting during `composer install` ([see diff](https://github.com/shopsys/project-base/commit/eedcf2ea9eaaef6c4f53a83fedbbd3c34428af83))
    - you should add a command to change the environment to the `ci` stage in `docker/php-fpm/Dockerfile` to keep it building in `prod` environment, otherwise it will be built in `dev` ([see diff](https://github.com/shopsys/project-base/commit/c974597237992b3083ed48f0937715de9cf5981d))

### [shopsys/shopsys]
- *(MacOS only)* [#503 updated docker-sync configuration](https://github.com/shopsys/shopsys/pull/503/)
    - run `docker-compose down` to turn off your containers
    - run `docker-sync clean` so your volumes will be removed
    - remove these lines from `docker-compose.yml`
        ```
        shopsys-framework-postgres-data-sync:
            external: true
        shopsys-framework-elasticsearch-data-sync:
            external: true
        ```
    - remove these lines from `docker-sync.yml`
        ```
        shopsys-framework-postgres-data-sync:
            src: './project-base/var/postgres-data/'
            host_disk_mount_mode: 'cached'
         shopsys-framework-elasticsearch-data-sync:
            src: './project-base/var/elasticsearch-data/'
            host_disk_mount_mode: 'cached'
        ```
    - *(monorepo only)* add `shopsys-framework-microservice-product-search-sync` and `shopsys-framework-microservice-product-search-export-sync` volumes to `docker-compose.yml` for `php-fpm` service
        ```
        services:
            # ...
            php-fpm:
                # ...
                volumes:
                    # ...
                    - shopsys-framework-microservice-product-search-sync:/var/www/html/microservices/product-search
                    - shopsys-framework-microservice-product-search-export-sync:/var/www/html/microservices/product-search-export
        ```
    - run `docker-sync start` to create volumes
    - run `docker-compose up -d --force-recreate` to start application again
- [#533 main php-fpm container now uses multi-stage build feature](https://github.com/shopsys/shopsys/pull/533)
    - the Dockerfile for `php-fpm` has changed, update your `docker-compose.yml` and `docker/php-fpm/Dockerfile` accordingly
        - copy [`docker/php-fpm/Dockerfile`](https://github.com/shopsys/shopsys/blob/master/project-base/docker/php-fpm/Dockerfile) from GitHub
        - update the build config in `docker-compose.yml` ([changes in version and build config can be seen in the PR](https://github.com/shopsys/shopsys/pull/533/files#diff-1aa104f9fc120d0743883a5ba02bfe21))
    - rebuild images by running `docker-compose up -d --build`
- *(optional)* rename Database tests to Functional tests
    - rename base class `DatabaseTestCase` to `TransactionFunctionalTestCase`
    - rename test namespace `Database` to `Functional`
    - rename phing target `tests-db` to `tests-functional`
    - you can follow [#541 Rename database tests to functional tests](https://github.com/shopsys/shopsys/pull/541)
- [#530 - Update of installation for production via docker](https://github.com/shopsys/shopsys/pull/530)
    - update `docker-compose.yml` on production server with the new configuration from updated [`docker-compose.prod.yml`](./project-base/docker/conf/docker-compose.prod.yml.dist) file
    - update `nginx.conf` with configuration from updated [`nginx.conf`](./project-base/docker/nginx/nginx.conf)
- [#545 - Part of the application build is now contained in the build of the image](https://github.com/shopsys/shopsys/pull/545)
    - the Dockerfile for `php-fpm` has changed, update your `docker/php-fpm/Dockerfile`
    - rebuild image by running `docker-compose up -d --build`
    - files `build.xml` and `build-dev.xml` were updated to speed up deployment process of built docker images of php-fpm
    - installation guide for production via Docker was updated, now there is no need for the first part of the build phing target
    - file `docker/php-fpm/docker-php-entrypoint` was changed, update it according to [`project-base/docker/php-fpm/docker-php-entrypoint`](./project-base/docker/php-fpm/docker-php-entrypoint)
- [#547 - content-test directory is used instead of content during the tests](https://github.com/shopsys/shopsys/pull/547)
    - modify your `parameters_test.yml.dist`, `parameters_test.yml`, `paths.yml` according to this pull request so there will be used different directory for feeds, images, etc., during the tests
    - modify your `build-dev.xml`, add a new phing target `test-dirs-create` and add it as a dependency after each `dirs-create` in this file
    - modify your `build.xml`, phing target `wipe-excluding-logs`  according to this pull request so the directory `content-test` will be truncated too
    - modify your `nginx.conf`, change location for images from `^/content/images/` to `^/content(-test)?/images/`
    - modify your `routing_front.yml`, change configuration for routes `front_image`, `front_image_without_type` - replace `/content/` by `/%shopsys.content_dir_name%/`
- *(optional)* [#535 added .dockerignore files](https://github.com/shopsys/shopsys/pull/535)
    - if you're using Docker-sync, add the directories mentioned in the PR into `sync_exclude` section of your `docker-sync.yml` to make the synchronization faster
- [#580 Removed trailing whitespaces from markdown files ](https://github.com/shopsys/shopsys/pull/580)
    - remove these lines from `.dockerignore`
        ```
        # ignore the docs (along with .md files in root dir)
        *.md
        docs
        project-base/*.md
        project-base/docs
        ```
    - run `docker-compose down` to turn off your containers
    - *(MacOS, Windows only)*
        - run `docker-sync clean` so your volumes will be removed
        - remove excluding of `docs` folder from `docker-sync.yml`
        - run `docker-sync start` to create volumes
    - run `docker-compose up -d --build --force-recreate` to start application
    - phing target for checking and fixing standards has changed, update `build.xml` according to the changes
- *(optional)* [#551 - github token erase](https://github.com/shopsys/shopsys/pull/551)
    - you can stop providing the `github_oauth_token` in your `docker-compose.yml`

### [shopsys/coding-standards]
- there are a few new standards, i.e. [new fixers enabled](https://github.com/shopsys/shopsys/pull/573/files#diff-709e8469a9fc8c8b45f8b285ac1a4c92) in the `easy-coding-standard.yml` config that enforce using annotations for all your methods:
    - if you want to use the standards as well, let the fixers check and fix your code
        - on projects built from `shopsys/project-base` just run `php phing standards-fix`
        - on any other projects, where you do not have our phing targets available, run `vendor/bin/ecs check path/to/your/source-codes --fix`
    - if you do not want to use these standards, disable them in your custom configuration (`easy-coding-standard.yml` by default)

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
[shopsys/microservice-product-search]: https://github.com/shopsys/microservice-product-search
[shopsys/microservice-product-search-export]: https://github.com/shopsys/microservice-product-search-export
