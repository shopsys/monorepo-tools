## [From v7.0.0-beta4 to Unreleased](https://github.com/shopsys/shopsys/compare/v7.0.0-beta4...HEAD)
### [shopsys/framework]
- stop providing the option `is_group_container_to_render_as_the_last_one` to the `FormGroup` in your forms, the option was removed
    - the separators are rendered automatically since [PR #619](https://github.com/shopsys/shopsys/pull/619) was merged and the option hasn't been used anymore
- [#627 model service layer removal](https://github.com/shopsys/shopsys/pull/627)
    - please read upgrade instructions in [separate article](docs/upgrade/services-removal.md)
- [#688 renamed AdvancedSearchFacade to AdvancedSearchProductFacade](https://github.com/shopsys/shopsys/pull/688)
    - change usages of `AdvancedSearchFacade` to `AdvancedSearchProductFacade`

### [shopsys/project-base]
- [#697 fix unnecessary log error after login as admin on second PC](https://github.com/shopsys/shopsys/pull/697)
    - deleted `AdministratorRepository::findByIdAndLoginToken()` method, if you were using it, please implement it to your `project-base` by yourself.
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
