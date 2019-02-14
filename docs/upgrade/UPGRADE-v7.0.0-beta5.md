# [Upgrade from v7.0.0-beta4 to v7.0.0-beta5](https://github.com/shopsys/shopsys/compare/v7.0.0-beta4...v7.0.0-beta5)

This guide contains instructions to upgrade from version v7.0.0-beta4 to v7.0.0-beta5.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
### Infrastructure
- Google Cloud deploy using Terraform, Kustomize and Kubernetes ([#633](https://github.com/shopsys/shopsys/pull/633))
    - update your `.dockerignore` to ignore infrastructure files, follow [changes](https://github.com/shopsys/project-base/commit/5b861e1b065f79b9d415166af3cc78b5e4414334)
    - use specific images instead of variables, follow [changes](https://github.com/shopsys/project-base/commit/4a81b78ead6ba181059fc7448c659bd12b7d8d75)
    - *(low priority)* If you are using Kubernetes manifests for CI or deployment, follow changes done in manifests and ci `build_kubernetes.sh` script.
- add `!docker/nginx` line into `.dockerignore` file so `docker/nginx` directory is not excluded during building `php-fpm` image ([#674](https://github.com/shopsys/shopsys/pull/674))
- make sure your `webserver` service depends on `php-fpm` in your `docker-compose.yml` file so webserver will not fail on error `host not found in upstream php-fpm:9000` ([#679](https://github.com/shopsys/shopsys/pull/679))
- on a production server enable compression of static files ([#703](https://github.com/shopsys/shopsys/pull/703))
    - you can see example configuration in [Installation using Docker on Production Server](/docs/installation/installation-using-docker-on-production-server.md) guide
- *(low priority)* Switched to Debian PHP-FPM image ([#702](https://github.com/shopsys/shopsys/pull/702))
    - update your Dockerfile to extend from debian image, follow [changes](https://github.com/shopsys/project-base/commit/023d6f20f3d041dce09d381522bd6c438ed9fa59) and [fix #740](https://github.com/shopsys/shopsys/pull/740/files)
    - change `runAsUser` value in `webserver-php-fpm.yml` manifest to 33 as it is Debian default `www-data` UID
    - change owner of persisted folder on production server to 33 as it is Debian default `www-data` UID

### Configuration
- *(low priority)* for easier deployment to production, make the trusted proxies in `Shopsys\Boostrap` class loaded from DIC parameter `trusted_proxies` instead of being hard-coded ([#596](https://github.com/shopsys/shopsys/pull/596))
    - in your `Shopsys\Boostrap`, move the `Request::setTrustedProxies(...)` call along with `Kernel::boot()` so it's not run in console environment, like in [the PR #660](https://github.com/shopsys/shopsys/pull/660/files), otherwise console commands will trigger excessive logging
- *(low priority)* add support for custom prefixing in redis ([#673](https://github.com/shopsys/shopsys/pull/673))
    - add default value (e.g. empty string) for `REDIS_PREFIX` env variable to your `app/config/parameters.yml.dist`, `app/config/parameters.yml` (if you already have your parameters file), and to your `docker/php-fpm/Dockerfile`
    - modify your Redis configuration (`app/config/packages/snc_redis.yml`) by prefixing all the prefix values with the value of the env variable (`%env(REDIS_PREFIX)%`)

### Tools
- *(low priority)* drop `--verbose` from all easy-coding-standard phing targets (look for `${path.ecs.executable}`) as the package was upgraded in [#623](https://github.com/shopsys/shopsys/pull/623/) and now outputs name of each file checked in the verbose mode

### Application
- stop providing the option `is_group_container_to_render_as_the_last_one` to the `FormGroup` in your forms, the option was removed
    - the separators are rendered automatically since [#619](https://github.com/shopsys/shopsys/pull/619) was merged and the option hasn't been used anymore
- service layer was removed ([#627](https://github.com/shopsys/shopsys/pull/627))
    - please read upgrade instructions in [separate article](./services-removal.md)
- change usages of `Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFacade` to `Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchProductFacade` ([#688](https://github.com/shopsys/shopsys/pull/688))
- method `AdministratorRepository::findByIdAndLoginToken()` was deleted. If you were using it, please implement it to your `project-base` by yourself ([#697](https://github.com/shopsys/shopsys/pull/697))
- new macro `loadMoreButton` is integrated into `@ShopsysShop/Front/Inline/Paginator/paginator.html.twig` ([#579](https://github.com/shopsys/shopsys/pull/579))
    - update files based on commits from [`ajaxMoreLoader is updated and generalized`](https://github.com/shopsys/shopsys/pull/579/files) and [`fix cooperation between AjaxMoreLoader and AjaxFilter`](https://github.com/shopsys/shopsys/pull/752/files)
- fix wrong variable name in flash message ([#685](https://github.com/shopsys/shopsys/pull/685))
    - in `Front/OrderController::checkTransportAndPaymentChanges()`, fix the variable name in the flash message in `$transportAndPaymentCheckResult->isPaymentPriceChanged()` condition
    - dump translations using `php phing dump-translations` and fill in your translations for the new message ID
- Change `User` constructor in `PersonalDataExportXmlTest::createUser` to `__construct(UserData $userData, BillingAddress $billingAddress, ?DeliveryAddress $deliveryAddress, ?self $userByEmail)` ([#690](https://github.com/shopsys/shopsys/pull/690))
    - If you extend `User`, change your `User` entity constructor accordingly as well
- change usages of the renamed methods of `ProductOnCurrentDomainFacade` ([#610](https://github.com/shopsys/shopsys/pull/610))
    - from `getPaginatedProductDetailsInCategory` to `getPaginatedProductsInCategory`
    - from `getPaginatedProductDetailsForBrand` to `getPaginatedProductsForBrand`
    - from `getPaginatedProductDetailsForSearch` to `getPaginatedProductsForSearch`
- change usage of changed methods definitions in `Product` and `ProductFactory` ([#723](https://github.com/shopsys/shopsys/pull/723))
    - `Product::__construct` to `__construct(ProductData $productData, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory, array $variants = null)`
    - `Product::create` to `create(ProductData $productData, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory)`
    - `Product::createMainVariant` to `createMainVariant(ProductData $productData, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory, array $variants)`
    - `Product::addVariant` to `addVariant(self $variant, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory)`
    - `Product::addVariants` to `addVariants(array $variants, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory)`
    - `Product::addNewVariants` to `addNewVariants(array $currentVariants, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory)`
    - `ProductFactory::__construct` to `__construct(EntityNameResolver $entityNameResolver, ProductAvailabilityCalculation $productAvailabilityCalculation, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory)`
    - add second parameter `new ProductCategoryDomainFactory()` to `Product::create` and `Product::createMainVariant` in tests
        - `CartWatcherTest::testGetNotListableItemsWithVisibleButNotSellableProduct`
        - `CartItemTest::testIsSimilarItemAs`
        - `CartTest` functions `testRemoveItem` and `createProduct`
        - `ProductManualInputPriceTest` functions `testRecalculateInputPriceForNewVatPercentWithInputPriceWithoutVat` and `testRecalculateInputPriceForNewVatPercentWithInputPriceWithVat`
        - `ProductAvailabilityCalculationTest` functions `testCalculateAvailability`, `testCalculateAvailabilityMainVariant` and `testCalculateAvailabilityMainVariantWithNoSellableVariants`
- *(low priority)* in your `base.html.twig` template move non-essential javascript files at the bottom of a page (([#703](https://github.com/shopsys/shopsys/pull/703)))
    - we cannot provide exact instructions as we don't know your implementation, for an example take a look at [changes](https://github.com/shopsys/shopsys/pull/703/files#diff-4c948fb55a9ceba2f3070e572ac506f3)
    - you can use new simplified jquery-ui.min.js file, but please be aware that if your implementation uses more components from jQueryUI, you have to be extra careful
- *(low priority)* in order to remove white space of webpage after popup window shows up, change file located in [`src/Shopsys/ShopBundle/Resources/styles/front/common/layout/layout.less`](https://github.com/shopsys/shopsys/pull/710/files#diff-b6f30401eed85fcb59b3b1761855493b) by following instructions.
    - add new CSS attribute `width: 100%` to CSS class `.web`
    - add following code snippet to CSS class `.web--window-activated`
    ```
    @media @query-vl {
        overflow: unset;
    }
    ```
- *(low priority)* display svg icons collection correctly in grunt generated document for all browsers ([#645](https://github.com/shopsys/shopsys/pull/645))
    - add [`src/Shopsys/ShopBundle/Resources/views/Grunt/htmlDocumentTemplate.html`](https://github.com/shopsys/shopsys/pull/645/files#diff-2fa69709c5ba35cd2ad6c5de640d56f9) file from GitHub
    - update `src/Shopsys/ShopBundle/Resources/views/Grunt/gruntfile.js.twig` based on changes in [pull request #645](https://github.com/shopsys/shopsys/pull/645/files#diff-ff210e4f423be8bd6c88818d2bb2a8cd)
- check correctness and existence of some translation and validation constants in *.po files for passing automation tests
    - these should exist
        ```
        {1}Load next %loadNextCount% item|[2,Inf]Load next %loadNextCount% items
        The price of payment {{ paymentName }} changed during ordering process. Check your order, please.
        ```
    - these should not exist
        ```
        The price of payment {{ transportName }} changed during ordering process. Check your order, please.
        ```

## [shopsys/migrations]
- `GenerateMigrationsService` class was renamed to `MigrationsGenerator`, so change it's usage appropriately ([#627](https://github.com/shopsys/shopsys/pull/627))

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
