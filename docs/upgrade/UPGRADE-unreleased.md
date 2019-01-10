# [Upgrade from v7.0.0-beta4 to Unreleased](https://github.com/shopsys/shopsys/compare/v7.0.0-beta4...HEAD)

This guide contains instructions to upgrade from version v7.0.0-beta4 to Unreleased.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
### Infrastructure
- Google Cloud deploy using Terraform, Kustomize and Kubernetes ([#633](https://github.com/shopsys/shopsys/pull/633))
    - update your `.dockerignore` to ignore infrastructure files, follow [changes](https://github.com/shopsys/shopsys/pull/633/commits/5e507aa0aff44cb689b8d65fba58da53a8fafd1f)
    - use specific images instead of variables, follow [changes](https://github.com/shopsys/shopsys/pull/633/commits/84dee757f62f5ff7b9581d9a1dcccc4e496cf7eb)
    - *(optional)* If you are using Kubernetes manifests for CI or deployment, follow changes done in manifests and ci `build_kubernetes.sh` script.
- add `!docker/nginx` line into `.dockerignore` file so `docker/nginx` directory is not excluded during building `php-fpm` image ([#674](https://github.com/shopsys/shopsys/pull/674))
- make sure your `webserver` service depends on `php-fpm` in your `docker-compose.yml` file so webserver will not fail on error `host not found in upstream php-fpm:9000` ([#679](https://github.com/shopsys/shopsys/pull/679))

### Configuration
- *(optional)* for easier deployment to production, make the trusted proxies in `Shopsys\Boostrap` class loaded from DIC parameter `trusted_proxies` instead of being hard-coded ([#596](https://github.com/shopsys/shopsys/pull/596))
    - in your `Shopsys\Boostrap`, move the `Request::setTrustedProxies(...)` call along with `Kernel::boot()` so it's not run in console environment, like in [the PR #660](https://github.com/shopsys/shopsys/pull/660/files), otherwise console commands will trigger excessive logging
- *(optional)* add support for custom prefixing in redis ([#673](https://github.com/shopsys/shopsys/pull/673))
    - add default value (e.g. empty string) for `REDIS_PREFIX` env variable to your `app/config/parameters.yml.dist`, `app/config/parameters.yml` (if you already have your parameters file), and to your `docker/php-fpm/Dockerfile`
    - modify your Redis configuration (`app/config/packages/snc_redis.yml`) by prefixing all the prefix values with the value of the env variable (`%env(REDIS_PREFIX)%`)

### Application
- stop providing the option `is_group_container_to_render_as_the_last_one` to the `FormGroup` in your forms, the option was removed
    - the separators are rendered automatically since [#619](https://github.com/shopsys/shopsys/pull/619) was merged and the option hasn't been used anymore
- service layer was removed ([#627](https://github.com/shopsys/shopsys/pull/627))
    - please read upgrade instructions in [separate article](./services-removal.md)
- change usages of `Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFacade` to `Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchProductFacade` ([#688](https://github.com/shopsys/shopsys/pull/688))
- method `AdministratorRepository::findByIdAndLoginToken()` was deleted. If you were using it, please implement it to your `project-base` by yourself ([#697](https://github.com/shopsys/shopsys/pull/697))
- new macro `loadMoreButton` is integrated into `@ShopsysShop/Front/Inline/Paginator/paginator.html.twig` ([#579](https://github.com/shopsys/shopsys/pull/579))
    - update files based on commit from [`ajaxMoreLoader is updated and generalized`](https://github.com/shopsys/shopsys/pull/579/files)
- fix wrong variable name in flash message ([#685](https://github.com/shopsys/shopsys/pull/685))
    - in `Front/OrderController::checkTransportAndPaymentChanges()`, fix the variable name in the flash message in `$transportAndPaymentCheckResult->isPaymentPriceChanged()` condition
    - dump translations using `php phing dump-translations` and fill in your translations for the new message ID
- Change `User` constructor in `PersonalDataExportXmlTest::createUser` to `__construct(UserData $userData, BillingAddress $billingAddress, ?DeliveryAddress $deliveryAddress, ?self $userByEmail)` ([#690](https://github.com/shopsys/shopsys/pull/690))
    - If you extend `User`, change your `User` entity constructor accordingly as well
- *(optional)* display svg icons collection correctly in grunt generated document for all browsers ([#645](https://github.com/shopsys/shopsys/pull/645))
    - add [`src/Shopsys/ShopBundle/Resources/views/Grunt/htmlDocumentTemplate.html`](https://github.com/shopsys/shopsys/pull/645/files#diff-2fa69709c5ba35cd2ad6c5de640d56f9) file from GitHub
    - update `src/Shopsys/ShopBundle/Resources/views/Grunt/gruntfile.js.twig` based on changes in [pull request #645](https://github.com/shopsys/shopsys/pull/645/files#diff-ff210e4f423be8bd6c88818d2bb2a8cd)
- change usages of the renamed methods of `ProductOnCurrentDomainFacade` ([#610](https://github.com/shopsys/shopsys/pull/610))
    - from `getPaginatedProductDetailsInCategory` to `getPaginatedProductsInCategory`
    - from `getPaginatedProductDetailsForBrand` to `getPaginatedProductsForBrand`
    - from `getPaginatedProductDetailsForSearch` to `getPaginatedProductsForSearch`

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
