# UPGRADING
## Recommended way of upgrading
Since these are 3 possible scenarios how you can use shopsys, instructions are divided by these scenarios.

### You use our packages only
Follow instructions in relevant sections, eg. `shopsys/coding-standards`, `shopsys/microservice-product-search`.

### You are using monorepo
Follow instructions in the section `shopsys/shopsys`.

### You are developing a project based on project-base
* upgrade only your composer dependencies and follow instructions
* if you want update your project with the changes from [shopsys/project-base], you can follow the *(optional)* instructions or cherry-pick from the repository whatever is relevant for you but we do not recommend rebasing or merging everything because the changes might not be compatible with your project as it probably evolves in time
* check all instructions in all sections, any of them could be relevant for you
* upgrade locally first. After you fix all issues caused by the upgrade, commit your changes and then continue with upgrading application on a server
* upgrade one version at a time:
    * Start with a working application
    * Upgrade to the next version
    * Fix all issues
    * Repeat
* typical upgrade sequence should be:
    * `docker-compose down`
    * follow upgrade notes for `docker-compose.yml`, `Dockerfile`, docker containers, `nginx.conf`, `php.ini`
    * change all the microservices image versions in your `docker-compose.yml` to version you are upgrading to
        eg. `image: shopsys/microservice-product-search:v7.0.0-beta1`
    * `docker-compose up -d`
    * update shopsys framework dependencies in `composer.json` to version you are upgrading to
        eg. `"shopsys/framework": "v7.0.0-beta1"`
    * `composer update`
    * follow all upgrade notes you have not done yet
    * `php phing clean`
    * `php phing db-migrations`
    * commit your changes
* even we care a lot about these instructions, it is possible we miss something. In case something doesn't work after the upgrade, you'll find more information in the [CHANGELOG](CHANGELOG.md)

There is a list of all the repositories maintained by monorepo, changes in log below are ordered as this list:

* [shopsys/framework]
* [shopsys/project-base]
* [shopsys/shopsys]
* [shopsys/coding-standards]
* [shopsys/form-types-bundle]
* [shopsys/http-smoke-testing]
* [shopsys/migrations]
* [shopsys/monorepo-tools]
* [shopsys/plugin-interface]
* [shopsys/product-feed-google]
* [shopsys/product-feed-heureka]
* [shopsys/product-feed-heureka-delivery]
* [shopsys/product-feed-zbozi]
* [shopsys/microservice-product-search]
* [shopsys/microservice-product-search-export]

## [From v7.0.0-beta4 to Unreleased]
### [shopsys/framework]
- stop providing the option `is_group_container_to_render_as_the_last_one` to the `FormGroup` in your forms, the option was removed
    - the separators are rendered automatically since [PR #619](https://github.com/shopsys/shopsys/pull/619) was merged and the option hasn't been used anymore
- [#627 model service layer removal](https://github.com/shopsys/shopsys/pull/627)
    - please read upgrade instructions in [separate article](docs/upgrade/services-removal.md)
- [#688 renamed AdvancedSearchFacade to AdvancedSearchProductFacade](https://github.com/shopsys/shopsys/pull/688)
    - change usages of `AdvancedSearchFacade` to `AdvancedSearchProductFacade`

### [shopsys/project-base]
- [#633 Google Cloud deploy using Terraform, Kustomize and Kubernetes](https://github.com/shopsys/shopsys/pull/633)
    - update your `.dockerignore` to ignore infrastructure files, follow [changes](https://github.com/shopsys/shopsys/pull/633/commits/5e507aa0aff44cb689b8d65fba58da53a8fafd1f)
    - use specific images instead of variables, follow [changes](https://github.com/shopsys/shopsys/pull/633/commits/84dee757f62f5ff7b9581d9a1dcccc4e496cf7eb)
    - *(optional)* If you are using Kubernetes manifests for CI or deployment, follow changes done in manifests and ci `build_kubernetes.sh` script. 
- *(optional)* [#596 Trusted proxies are now configurable in parameters.yml file](https://github.com/shopsys/shopsys/pull/596)
    - for easier deployment to production, make the trusted proxies in `Shopsys\Boostrap` class loaded from DIC parameter `trusted_proxies` instead of being hard-coded
    - in your `Shopsys\Boostrap`, move the `Request::setTrustedProxies(...)` call along with `Kernel::boot()` so it's not run in console environment, like in [the PR #660](https://github.com/shopsys/shopsys/pull/660/files), otherwise console commands will trigger excessive logging
- [#579 - ajaxMoreLoader is generalized](https://github.com/shopsys/shopsys/pull/579)
    - new macro `loadMoreButton` is integrated into `@ShopsysShop/Front/Inline/Paginator/paginator.html.twig`, update files based on commit from [`ajaxMoreLoader is updated and generalized`](https://github.com/shopsys/shopsys/pull/579/files)
- *(optional)* [#645 SVG icons in generated document](https://github.com/shopsys/shopsys/pull/645)
    - to display svg icons collection correctly in grunt generated document for all browsers please add `src/Shopsys/ShopBundle/Resources/views/Grunt/htmlDocumentTemplate.html` file and update `src/Shopsys/ShopBundle/Resources/views/Grunt/gruntfile.js.twig` based on changes in this pull request
- [#674 - Dockerignore needs to accept nginx configuration for production on docker](https://github.com/shopsys/shopsys/pull/674)
    - add `!docker/nginx` line into `.dockerignore` file so `docker/nginx` directory is not excluded during building `php-fpm` image
- [#685 - fix wrong variable name in flash message](https://github.com/shopsys/shopsys/pull/685)
    - in `Front/OrderController::checkTransportAndPaymentChanges()`, fix the variable name in the flash message in `$transportAndPaymentCheckResult->isPaymentPriceChanged()` condition 
    - dump translations using `php phing dump-translations` and fill in your translations for the new message ID 
- *(optional)* [#673 added support for custom prefixing in redis](https://github.com/shopsys/shopsys/pull/673)
    - add default value (e.g. empty string) for `REDIS_PREFIX` env variable to your `app/config/parameters.yml.dist`, `app/config/parameters.yml` (if you already have your parameters file), and to your `docker/php-fpm/Dockerfile`
    - modify your Redis configuration (`app/config/packages/snc_redis.yml`) by prefixing all the prefix values with the value of the env variable (`%env(REDIS_PREFIX)%`)

### [shopsys/shopsys]
- [#651 It's possible to add index prefix to elastic search](https://github.com/shopsys/shopsys/pull/651)
    - either rebuild your Docker images with `docker-compose up -d --build` or add `ELASTIC_SEARCH_INDEX_PREFIX=''` to your `.env` files in the microservice root directories, otherwise all requests to the microservices will throw `EnvNotFoundException` 

### [shopsys/migrations]
 - [#627 model service layer removal](https://github.com/shopsys/shopsys/pull/627)
    - `GenerateMigrationsService` class was renamed to `MigrationsGenerator`, so change it's usage appropriately.

## [From v7.0.0-beta3 to v7.0.0-beta4]
### [shopsys/project-base]
- [#616 - services.yml: automatic registration of classes with suffix "Repository" in namespace ShopBundle\Model\ ](https://github.com/shopsys/shopsys/pull/616)
    - modify your `src/Shopsys/ShopBundle/Resources/config/services.yml`, change the resource for automatic registration of Model services from `resource: '../../Model/**/*{Facade,Factory}.php'` to `resource: '../../Model/**/*{Facade,Factory,Repository}.php'`
- set `ENV COMPOSER_MEMORY_LIMIT=-1` in base stage in your `docker/php-fpm/Dockerfile` as composer consumes huge amount of memory during dependencies installation
    - see [#635 - allow composer unlimited memory](https://github.com/shopsys/shopsys/pull/635/files)

## [From 7.0.0-beta2 to v7.0.0-beta3]
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

## [From 7.0.0-beta1 to 7.0.0-beta2]
### [shopsys/project-base]
- *(optional)* [#497 adding php.ini to image is now done only in dockerfiles](https://github.com/shopsys/shopsys/pull/497)
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
- *(optional)* when you upgrade `codeception/codeception` to version `2.5.0`, you have to change parameter `populate` to `true` in `tests/ShopBundle/Acceptance/acceptance.suite.yml`
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
- [#513 - Manipulation with domains is modified and documented now](https://github.com/shopsys/shopsys/pull/513)
    - modify your `build.xml` according to this pull request
        - recalculations will be processed after `create-domains-data` command
        - creation of some database functions was moved from `create-domains-data` phing target to a new phing target `create-domains-db-functions`
    - modify your `build-dev.xml` according to this pull request
        - creation of some database functions was moved from `test-create-domains-data` phing target to a new phing target `test-create-domains-db-functions`
- *(optional)* speed up composer in your `php-fpm` container by adding `RUN composer global require hirak/prestissimo` into `docker/php-fpm/Dockerfile`
- *(optional)* to enable logging of errors in the `php-fpm` container, add `log_errors = true` to `docker/php-fpm/php-ini-overrides.ini`
- *(optional)* make changes in `composer.json`:
    - remove conflict `"codeception/stub"`, the conflicting version doesn't exist anymore and conflict is solved
    - change conflict of `"symfony/dependency-injection"` to `"3.4.15|3.4.16"`

## [From 7.0.0-alpha6 to 7.0.0-beta1]
### [shopsys/framework]
- *(optional)* [#468 - Setting for docker on mac are now more optimized](https://github.com/shopsys/shopsys/pull/468)
    - if you use the Shopsys Framework with docker on the platform Mac, modify your
      [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-beta1/docker/conf/docker-compose-mac.yml.dist)
      and [`docker-sync.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-beta1/docker/conf/docker-sync.yml.dist) according to the new templates
    - next restart docker-compose and docker-sync
- *(optional)* [#483 - updated info about Docker on Mac](https://github.com/shopsys/shopsys/pull/483)
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

## [From 7.0.0-alpha5 to 7.0.0-alpha6]
### [shopsys/framework]
- check for usages of `TransportEditFormType` - it was removed and all it's attributes were moved to `TransportFormType` so use this form instead
- check for usages of `PaymentEditFormType` - it was removed and all it's attributes were moved to `PaymentFormType` so use this form instead
- check for usages of `ProductEditFormType` - it was removed and all it's attributes were moved to `ProductFormType` so use this form instead
- pay attention to javascripts bound to your forms as well as the elements' [names and ids has changed #428](https://github.com/shopsys/shopsys/pull/428)
    - e.g. change id from `#product_edit_form_productData` to `#product_form`
    - check also your tests, you need to change names and ids of elements too
- PHP-FPM and microservice containers now expect a GitHub OAuth token set via a build argument, so it is not necessary to provide it every time those containers are rebuilt
    - see the `github_oauth_token` argument setting in the [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha6/project-base/docker/conf/docker-compose.yml.dist#L33) template you used and replicate it in your `docker-compose.yml`
        - *since `docker-compose.yml` is not versioned, apply changes also in your `docker-compose.yml.dist` templates so it is easier to upgrade for your team members or for server upgrade*
    - replace the `place-your-token-here` string by the token generated on [Github -> Settings -> Developer Settings -> Personal access tokens](https://github.com/settings/tokens/new?scopes=repo&description=Composer+API+token)
- as there were changes in the Dockerfiles, replace `php-fpm` dockerfile by a new version:
    - copy [`docker/php-fpm/Dockerfile`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha6/project-base/docker/php-fpm/Dockerfile) from github
    - rebuild images `docker-compose up -d --build`
    - if you are in monorepo with microservices, just run `docker-compose up -d --build`
- [#438 - Attribute telephone moved from a billing address to the personal data of a user](https://github.com/shopsys/shopsys/pull/438)
    - this change can affect your extended forms and entities, reflect this change into your project

### [shopsys/project-base]
- [Microservice Product Search Export](https://github.com/shopsys/microservice-product-search-export) was added and it needs to be installed and run
    - check changes in the `docker-compose.yml` template you used and replicate them, there is a new container `microservice-product-search-export`
    - `parameters.yml.dist` contains new parameter `microservice_product_search_export_url`
        - add `microservice_product_search_export_url: 'http://microservice-product-search-export:8000'` into your `parameters.yml.dist`
        - execute `composer install` *(it will copy parameter into `parameters.yml`)*
- *(optional)* instead of building the Docker images of the microservices yourself, you can use pre-built images on Docker Hub (see the [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha6/project-base/docker/conf) template you used)
- [#438 - Attribute telephone moved from a billing address to the personal data of a user](https://github.com/shopsys/shopsys/pull/438)
    - edit `ShopBundle/Form/Front/Customer/BillingAddressFormType` - remove `telephone`
    - edit `ShopBundle/Form/Front/Customer/UserFormType` - add `telephone`
    - edit twig templates and tests in such a way as to reflect the movement of `telephone` attribute according to the [pull request](https://github.com/shopsys/shopsys/pull/438)
- *(optional)* to use custom postgres configuration check changes in the [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha6/project-base/docker/conf) templates and replicate them, there is a new volume for `postgres` container
    - PR [Improve Postgres configuration to improve performance](https://github.com/shopsys/shopsys/pull/444)
    - Stop running containers `docker-compose down`
    - Move data from `project-base/var/postgres-data` into `project-base/var/postgres-data/pgdata`. The directory must have correct permission depending on your OS.
      To provide you with a better image of what exactly needs to be done, there are instructions for Ubuntu:
        - `sudo su`
        - `cd project-base/var/postgres-data/`
        - trick to create directory `pgdata` with correct permissions
            - `cp -rp base/ pgdata`
            - `rm -fr pgdata/*`
        - `shopt -s extglob dotglob`
        - `mv !(pgdata) pgdata`
        - `shopt -u dotglob`
        - `exit`
    - Start containers `docker-compose up -d`
- *(optional)* configuration files (`config.yml`, `config_dev.yml`, `config_test.yml`, `security.yml` and `wysiwyg.yml`) has been split into packages config files, for details [see #449](https://github.com/shopsys/shopsys/pull/449)
    - extract each section into own config file
        - eg. from `config.yml` extract `doctrine:` section into file `packages/doctrine.yml`
        - eg. from `config_dev.yml` extract `assetic:` section info file `packages/dev/assetic.yml`
        - and also split `wysiwyg.yml` into `packages/*.yml`
            - *(since `config.yml` will include all files in `packages/`, splitted `wysiwyg.yml` will be included automatically)*
    - move `security.yml` to `packages/security.yml`
    - the only thing that have to be left in the original configuration files is the import of these new configuration files
        - eg. `config_dev.yml` will contain only
            ```
            imports:
                 - { resource: packages/dev/*.yml }
            ```
- phing targets and console commands for working with elasticsearch were renamed, so rename them in `build.xml`, `build-dev.xml`. Also if you call them from other places, rename calling too:
    - phing targets:
        - `elasticsearch-indexes-create` -> `microservice-product-search-create-structure`
        - `elasticsearch-indexes-delete` -> `microservice-product-search-delete-structure`
        - `elasticsearch-indexes-recreate` -> `microservice-product-search-recreate-structure`
        - `elasticsearch-products-export` -> `microservice-product-search-export-products`
    - console commands:
        - `shopsys:elasticsearch:create-indexes` -> `shopsys:microservice:product-search:create-structure`
        - `shopsys:elasticsearch:delete-indexes` -> `shopsys:microservice:product-search:delete-structure`
        - `shopsys:elasticsearch:export-products` -> `shopsys:microservice:product-search:export-products`
- run `php phing ecs-fix` to apply new coding standards - [keep class spacing consistent #384](https://github.com/shopsys/shopsys/pull/384)

### [shopsys/shopsys]
- when upgrading your installed [monorepo](docs/introduction/monorepo.md), you'll have to change the build context for the images of the microservices in `docker-compose.yml`
    - `build.context` should be the root of the microservice (eg. `microservices/product-search-export`)
    - `build.dockerfile` should be `docker/Dockerfile`
    - execute `docker-compose up -d --build`, microservices should be up and running

## [From 7.0.0-alpha4 to 7.0.0-alpha5]

### [shopsys/framework]
- for [product search via Elasticsearch](/docs/introduction/product-search-via-elasticsearch.md), you'll have to:
    - check changes in the [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docker/conf) template you used and replicate them, there is a new container with Elasticsearch
        - *since `docker-compose.yml` is not versioned, apply changes also in your `docker-compose.yml.dist` templates so it is easier to upgrade for your team members or for server upgrade*
    - since the fully installed and ready [Microservice Product Search](https://github.com/shopsys/microservice-product-search) is a necessary condition for the Shopsys Framework to run, the installation procedure of this microservice is a part of Shopsys Framework [installation guide](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docs/installation/installation-using-docker-application-setup.md)
        - alternately you can use [docker microservice image](https://github.com/shopsys/demoshop/blob/4946be4111d7fae4d7497921f9a4ec9aed24db42/docker/conf/docker-compose.yml.dist#L104-L110) that require no installation
    - run `docker-compose up -d`
    - update composer dependencies `composer update`
    - create Elasticsearch indexes by running `php phing elasticsearch-indexes-create`
    - export products into Elasticsearch by `php phing elasticsearch-products-export`
- `ProductFormType` [is extensible now #375](https://github.com/shopsys/shopsys/pull/375). If you extended the product form, you have to:
    - move form parts into right subsections, eg. [this change on demoshop](https://github.com/shopsys/demoshop/commit/62ae3dd3f2880f4c0d2a5ec33747c3f2f8448f41)
    - if you don't have custom rendering, remove your template for form
    - if you have custom rendering, change rendering of these parts as they are now in subsections
    - as the form changed structure, you have to also fix tests. see [this change on demoshop](https://github.com/shopsys/demoshop/commit/62ae3dd3f2880f4c0d2a5ec33747c3f2f8448f41)
        - form fields changed names and also ids

#### PostgreSQL upgrade:
We decided to move onto a newer version of PostgreSQL.

These steps are for migrating your data onto newer version of postgres and are inspired by [official documentation](https://www.postgresql.org/docs/10/static/upgrading.html):

If you are running your project natively then just follow [official instructions](https://www.postgresql.org/docs/10/static/upgrading.html),
if you are using docker infrastructure you can follow steps written below.

1. create a backup of your database by executing::

    `docker exec -it shopsys-framework-postgres pg_dumpall > backupfile`

1. apply changes in `docker-compose.yml`, you can find them in a new version of [`docker-compose.yml.dist`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docker/conf) templates

    *Note: select correct `docker-compose` according to your operating system*

    *since `docker-compose.yml` is not versioned, apply changes also in your `docker-compose.yml.dist` templates so it is easier to upgrade for your team members or for server upgrade*

1. update version of `database_server_version` from *9.5* to *10.5* in your `parameters.yml`

1. stop containers and delete old data:

    `docker-compose down`

    `rm -rf <project-root-path>/var/postgres-data/*`

1. use a new version of `php-fpm` container:

    `curl -L https://github.com/shopsys/shopsys/raw/v7.0.0-alpha5/project-base/docker/php-fpm/Dockerfile --output docker/php-fpm/Dockerfile`

    `docker-compose build php-fpm`

1. start new docker-compose stack with newer version of postgres by just recreating your containers:

    `docker-compose up -d --force-recreate`

1. copy backup into postgres container root folder

    `docker cp backupfile shopsys-framework-postgres:/`

1. restore you data:

    `docker exec -it shopsys-framework-postgres psql -d postgres -f backupfile`

1. delete backup file:

    `docker exec -it shopsys-framework-postgres rm backupfile`

1. recreate collations:

    `docker exec shopsys-framework-php-fpm ./phing db-create test-db-create`

### [shopsys/project-base]
- added [Microservice Product Search](https://github.com/shopsys/microservice-product-search)
    - check changes in the [`docker-compose.yml`](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docker/conf) template you used and replicate them, there is a new container `microservice-product-search`
        - *since `docker-compose.yml` is not versioned, apply changes also in your `docker-compose.yml.dist` templates so it is easier to upgrade for your team members or for server upgrade*
        - follow [installation guide](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/docs/installation/installation-using-docker-application-setup.md) to install microservice
          or use [docker microservice image](https://github.com/shopsys/demoshop/blob/4946be4111d7fae4d7497921f9a4ec9aed24db42/docker/conf/docker-compose.yml.dist#L104-L110) that require no installation
    - into `parameters.yml.dist` add a new parameter `microservice_product_search_url`:
        - `microservice_product_search_url: 'http://microservice-product-search:8000'`
        - and add it also into `parameters.yml`
    - modify a configuration in `services.yml` for:
        - `Shopsys\FrameworkBundle\Model\Product\Search\ProductSearchRepository`
        - `shopsys.microservice_client.product_search`
    - remove a configuration in `services.yml` for:
        - `Shopsys\FrameworkBundle\Model\Product\Search\ElasticsearchSearchClient`
        - `Shopsys\FrameworkBundle\Model\Product\Search\CachedSearchClient`
        - `Shopsys\FrameworkBundle\Model\Product\Search\SearchClient`
- *(optional)* standardize indentation in your yaml files
    - you can find yaml files with wrong indentation with regexp `^( {4})* {1,3}[^ ]`
- *(optional)* we added a new phing target that checks [availabitliy of microservices](https://github.com/shopsys/shopsys/blob/v7.0.0-alpha5/project-base/build-dev.xml#L726-L731).
  Feel free to include this target into your build process.
- add new themes to configuration `app/config/config.yml`, path `twig.form_themes`:
    ```
        - '@ShopsysFramework/Admin/Form/warningMessage.html.twig'
        - '@ShopsysFramework/Admin/Form/displayOnlyUrl.html.twig'
        - '@ShopsysFramework/Admin/Form/localizedFullWidth.html.twig'
        - '@ShopsysFramework/Admin/Form/productParameterValue.html.twig'
        - '@ShopsysFramework/Admin/Form/productCalculatedPrices.html.twig'
    ```

## [From 7.0.0-alpha3 to 7.0.0-alpha4]

### [shopsys/framework]
- move creation of data objects into factories
- already existing data object factories changed their signatures
- to change the last item in admin breadcrumb, use `BreadcrumbOverrider:overrideLastItem(string $label)` instead of `Breadcrumb::overrideLastItem(MenuItem $item)`
- if you've customized the admin menu by using your own `admin_menu.yml`, implement event listeners instead
    - see the [Adding a New Administration Page](/docs/cookbook/adding-a-new-administration-page.md) cookbook for details

### [shopsys/product-feed-google]
- move creation of data objects into factories
- already existing data object factories changed their signatures

### [shopsys/product-feed-heureka]
- move creation of data objects into factories
- already existing data object factories changed their signatures

### [shopsys/product-feed-zbozi]
- move creation of data objects into factories
- already existing data object factories changed their signatures

## [From 7.0.0-alpha2 to 7.0.0-alpha3]

### [shopsys/framework]
- classes in src/Components were revised, refactored and some of them were moved to model,
    for upgrading to newer version, you must go through commits done in [#272](https://github.com/shopsys/shopsys/pull/272) and reflect the changes of namespaces.
- FriendlyUrlToGenerateRepository: deleted. If you want to define your own data for friendly url generation, do it so by
    implementing the FriendlyUrlDataProviderInterface and tag your service with `shopsys.friendly_url_provider`.
- check changes in src/Model, all *editData*.php were merged into its *Data*.php relatives
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue
- access multi-domain attributes of entities via their main entity (instead of the usual entity detail)
    - entity domains (eg. `BrandDomain`) should be created, edited and directly accessed only in their main entities (eg. `Brand`)
    - see [#165 Different approach to multidomain entities](https://github.com/shopsys/shopsys/pull/165) for details
- `DomainsType` now uses array of booleans indexed by domain IDs instead of array of domain IDs, original behavior can be restored by adding model data transformer `IndexedBooleansToArrayOfIndexesTransformer`
- `CategoryDomain::$hidden` was changed to `CategoryDomain::$enabled` along with related methods (with negated value)
- `PaymentDomain` and `TransportDomain` are now created even for domains on which the entity should not be visible, check your custom queries that work with payments or transports
- instead of using `EntityManagerFacade::clear()` call `clear()` directly on the `EntityManager`
- all *Detail classes were removed:
    - use `CategoryWithLazyLoadedVisibleChildren` instead of `LazyLoadedCategoryDetail`
    - use `CategoryWithLazyLoadedVisibleChildrenFactory::createCategoriesWithLazyLoadedVisibleChildren()` instead of `CategoryDetailFactory::createLazyLoadedDetails()`
    - use `CategoryFacade::getCategoriesWithLazyLoadedVisibleChildrenForParent()` instead of `CategoryFacade::getVisibleLazyLoadedCategoryDetailsForParent()`
    - use `CategoryWithPreloadedChildren` instead of `CategoryDetail`
    - use `CategoryWithPreloadedChildrenFactory::createCategoriesWithPreloadedChildren()` instead of `CategoryDetailFactory::createDetailsHierarchy()`
    - use `CategoryFacade::getVisibleCategoriesWithPreloadedChildrenForDomain()` instead of `CategoryFacade::getVisibleCategoryDetailsForDomain()`
    - use `PaymentFacade::getIndependentBasePricesIndexedByCurrencyId()` instead of `PaymentDetail::$basePricesByCurrencyId`
    - use `TransportFacade::getIndependentBasePricesIndexedByCurrencyId()` instead of `TransportDetail::$basePricesByCurrencyId`
    - `ProductDetail::hasContentForDetailBox()` is not available anymore (it was useless)
    - use `ProductCachedAttributesFacade` for accessing product parameter values and selling price
    - in templates, use Twig function `getProductParameterValues(product)` instead of `productDetail.parameters`
    - in templates, use Twig function `getProductSellingPrice(product)` instead of `productDetail.sellingPrice`

### [shopsys/project-base]
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue

### [shopsys/coding-standards]
- create your custom `easy-coding-standard.yml` in your project root with your ruleset (you can use predefined ruleset as shown below)
- in order to run all checks, there is new unified way - execute `php vendor/bin/ecs check /path/to/project`
- see [EasyCodingStandard docs](https://github.com/Symplify/EasyCodingStandard#usage) for more information
#### Example of custom configuration file
```yaml
#easy-coding-standard.yml
imports:
    - { resource: '%vendor_dir%/shopsys/coding-standards/easy-coding-standard.yml' }
parameters:
    exclude_files:
        - '*/ignored_folder/*'
    skip:
        ObjectCalisthenics\Sniffs\Files\FunctionLengthSniff:
            - '*/src/file.php'
```

### [shopsys/product-feed-google]
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue

### [shopsys/product-feed-heureka]
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue

### [shopsys/product-feed-heureka-delivery]
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue

### [shopsys/product-feed-zbozi]
- Twig has been updated to version 2.4.8
    - https://symfony.com/blog/twig-how-to-upgrade-to-2-0-deprecation-notices-to-the-rescue

## [From 7.0.0-alpha1 to 7.0.0-alpha2]
### [shopsys/project-base]  
- check changes in the `docker-compose.yml` template you used, there were a couple of important changes you need to replicate
    - easiest way is to overwrite your `docker-compose.yml` with by the appropriate template
- on *nix systems, fill your UID and GID (you can run `id -u` and `id -g` to obtain them) into Docker build arguments `www_data_uid` and `www_data_gid` and rebuild your image via `docker-compose up --build`
- change owner of the files in shared volume to `www-data` from the container by running `docker exec -u root shopsys-framework-php-fpm chown -R www-data /var/www/html`
    - the user has shared UID, so you will be able to access it as well from the host machine
    - shared volume with postgres data should be owned by `postgres` user: `docker exec -u root shopsys-framework-php-fpm chown -R postgres /var/www/html/var/postgres-data`
- if you were using a mounted volume to share Composer cache with the container, change the target directory from `/root/.composer` to `/home/www-data/.composer`
    - in such case, you should change the owner as well by running `docker exec -u root shopsys-framework-php-fpm chown -R www-data /home/www-data/.composer`

## Before monorepo
Before we managed to implement monorepo for our packages, we had slightly different versions for each of our package,
that's why is this section formatted differently.  

### [shopsys/product-feed-heureka]
#### From 0.4.2 to 0.5.0
- requires possibility of extending the CRUD of categories via `shopsys.crud_extension` of type `category`
- requires update of [shopsys/plugin-interface](https://github.com/shopsys/plugin-interface) to version `^0.3.0`
and [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface) to `^0.5.0`

#### From 0.4.0 to 0.4.1
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-030-to-040)

#### From 0.2.0 to 0.4.0
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-020-to-030)

#### From 0.1.0 to 0.2.0
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-010-to-020)

### [shopsys/product-feed-zbozi]
#### From 0.4.0 to 0.4.1
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-030-to-040)

#### From 0.3.0 to 0.4.0
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-020-to-030)

#### From 0.1.0 to 0.2.0
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-010-to-020)

### [shopsys/product-feed-heureka-delivery]
#### From 0.2.0 to 0.2.1
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-030-to-040)

#### From 0.1.1 to 0.2.0
- See [Upgrading of shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-010-to-030)

### [shopsys/product-feed-interface]
#### From 0.4.0 to 0.5.0
- implement method `getMainCategoryId()` in your implementations of `StandardFeedItemInterface`.

#### From 0.3.0 to 0.4.0
- implement method `isSellingDenied()` for all implementations of `StandardFeedItemInterface`.
- you have to take care of filtering of non-sellable items in implementations of `FeedConfigInterface::processItems()`
in your product feed plugin because the instances of `StandardFeedItemInterface` passed as an argument can be non-sellable now.
- implement method `getAdditionalInformation()` in your implementations of `FeedConfigInterface`.
- implement method `getCurrencyCode()` in your implementations of `StandardFeedItemInterface`.

#### From 0.2.0 to 0.3.0
- remove method `getFeedItemRepository()` from all implementations and usages of `FeedConfigInterface`.

#### From 0.1.0 to 0.2.0
- Rename all implementations and usages of `FeedItemInterface::getItemId()` to `getId()`.
- Rename all implementations and usages of `FeedItemCustomValuesProviderInterface` to `HeurekaCategoryNameProviderInterface`.
- If you are using custom values in your implementation, you need to implement interfaces from package [shopsys/plugin-interface](https://github.com/shopsys/plugin-interface) (see [how to work with data storage interface](https://github.com/shopsys/plugin-interface#data-storage)).

### [shopsys/plugin-interface]
#### From 0.2.0 to 0.3.0
- all implementations of `DataStorageInterface` now must have implemented method `getAll()` for getting all saved data indexed by keys

### [shopsys/project-base]
#### From 2.0.0-beta.21.0 to 7.0.0-alpha1  
- manual upgrade from this version will be very hard because of BC-breaking extraction of [shopsys/framework](https://github.com/shopsys/framework)  
    - at this moment the core is not easily extensible by your individual functionality  
    - before upgrading to the new architecture you should upgrade to Dockerized architecture of `2.0.0-beta.21.0`  
    - the upgrade will require overriding or extending of all classes now located in  
    [shopsys/framework](https://github.com/shopsys/framework) that you customized in your forked repository  
    - it would be wise to wait with the upgrade until the newly build architecture has matured  
- update custom tests to be compatible with phpunit 7. For further details visit phpunit release announcements [phpunit 6](https://phpunit.de/announcements/phpunit-6.html) and [phpunit 7](https://phpunit.de/announcements/phpunit-7.html)

#### From 2.0.0-beta.20.0 to 2.0.0-beta.21.0  
- do not longer use Phing targets standards-ci and standards-ci-diff, use standards and standards-diff instead

#### From 2.0.0-beta.17.0 to 2.0.0-beta.18.0
- use `SimpleCronModuleInterface` and `IteratedCronModuleInterface` from their new namespace `Shopsys\Plugin\Cron` (instead of `Shopsys\FrameworkBundle\Component\Cron`)

#### From 2.0.0-beta.16.0 to 2.0.0-beta.17.0  
- coding standards for JS files were added, make sure `phing eslint-check` passes  
    (you can run `phing eslint-fix` to fix some violations automatically)  

#### From 2.0.0-beta.15.0 to 2.0.0-beta.16.0  
- all implementations of `Shopsys\ProductFeed\FeedItemRepositoryInterface` must implement interface `Shopsys\FrameworkBundle\Model\Feed\FeedItemRepositoryInterface` instead  
    - the interface was moved from [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/) to core  
- parameter `email_for_error_reporting` was renamed to `error_reporting_email_to` in `app/config/parameter.yml.dist`,  
    you will be prompted to fill it out again during `composer install`  
- all implementations of `StandardFeedItemInterface` must implement methods `isSellingDenied()` and `getCurrencyCode()`, see [product-feed-interface](https://github.com/shopsys/product-feed-interface/blob/master/UPGRADE.md#from-030-to-040)

### [shopsys/coding-standards]
#### From 3.x to 4.0
- In order to run all checks, there is new unified way - execute `php vendor/bin/ecs check /path/to/project --config=vendor/shopsys/coding-standards/easy-coding-standard.neon`
    - If you are overriding rules configuration in your project, it is necessary to do so in neon configuration file, see [example bellow](./example-of-custom-configuration-file).
    - See [EasyCodingStandard docs](https://github.com/Symplify/EasyCodingStandard#usage) for more information
##### Example of custom configuration file
###### Version 3.x and lower
```php
// custom phpcs-fixer.php_cs
$originalConfig = include __DIR__ . '/../vendor/shopsys/coding-standards/build/phpcs-fixer.php_cs';

$originalConfig->getFinder()
    ->exclude('_generated');

return $originalConfig;
```
###### Version 4.0 and higher
```neon
#custom-coding-standard.neon
includes:
    - vendor/symplify/easy-coding-standard/config/psr2-checkers.neon
    - vendor/shopsys/coding-standards/shopsys-coding-standard.neon
parameters:
    exclude_files:
        - *_generated/*

```
[From v7.0.0-beta4 to Unreleased]: https://github.com/shopsys/shopsys/compare/v7.0.0-beta4...HEAD
[From v7.0.0-beta3 to v7.0.0-beta4]: https://github.com/shopsys/shopsys/compare/v7.0.0-beta3...v7.0.0-beta4
[From 7.0.0-beta2 to v7.0.0-beta3]: https://github.com/shopsys/shopsys/compare/v7.0.0-beta2...v7.0.0-beta3
[From 7.0.0-beta1 to 7.0.0-beta2]: https://github.com/shopsys/shopsys/compare/v7.0.0-beta1...v7.0.0-beta2
[From 7.0.0-alpha6 to 7.0.0-beta1]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha6...v7.0.0-beta1
[From 7.0.0-alpha5 to 7.0.0-alpha6]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha5...v7.0.0-alpha6
[From 7.0.0-alpha4 to 7.0.0-alpha5]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha4...v7.0.0-alpha5
[From 7.0.0-alpha3 to 7.0.0-alpha4]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha3...v7.0.0-alpha4
[From 7.0.0-alpha2 to 7.0.0-alpha3]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha2...v7.0.0-alpha3
[From 7.0.0-alpha1 to 7.0.0-alpha2]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha1...v7.0.0-alpha2

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
