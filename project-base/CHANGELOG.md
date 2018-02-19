# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- PHPStan support (@mhujer)
	- currently analysing source code by level 0
- PHP 7.2 support (@TomasLudvik)
- Uniformity of PHP and Postgres timezones is checked during the build (@Miroslav-Stopka)
- in `TEST` environment `Domain` is created with all instances of `DomainConfig` having URL set to `%overwrite_domain_url%`
    - parameter is set only in `parameters_test.yml` as it is only relevant in `TEST` environment
    - overwriting can be switched off by setting the parameter to `~` (null in Yaml)
    - overwriting the domain URL is necessary for Selenium acceptance tests running in Docker
- LegalConditionsSetting: added privacy policy article selection (@stanoMilan)
    - customers need to agree with privacy policy while registring, sending contact form and completing order process
- SubscriptionFormType: added required privacy policy agreement checkbox (@simara-svatopluk)
- subscription form: added link to privacy policy agreement article (@simara-svatopluk)
- NewsletterController now exports date of subscription to newsletter (@simara-svatopluk)
- `services_command.yml` to set Commands as services (@TomasLudvik)

### Changed
- cache is cleared before PHPUnit tests only when run via [Phing targets](docs/introduction/phing-targets.md), not when run using `phpunit` directly (@PetrHeinz)
- PHPUnit tests now fail on warning (@TomasLudvik)
- end of support of PHP 7.0 (@TomasLudvik)
- renamed TermsAndCondition to LegalCondition to avoid multiple classes for legal conditions agreements (@stanoMilan) 
- emails with empty subject or body are no longer sent (@stanoMilan)
- postgresql-client is installed in [php-fpm/dockerfile](docker/php-fpm/Dockerfile) for `pg_dump` function (@MattCzerner)
    - postgresql was downgraded to 9.5 because of compatibility with postgresql-client
- docker-compose: added container_name to smtp-server and adminer (@MattCzerner)
- configuration of Docker Compose tweaked for easier development (@MattCzerner)
    - `docker-compose.yml` is added to `.gitignore` for everyone to be able to make individual changes
    - the predefined templates are now in `/docker/conf` directory
    - `adminer` container uses port 1100 by default (as 1000 is often already in use)
    - Docker Sync is used only in configuration for MacOS as only there it is needed
    - `postgres` container is created with a volume for data persistence (in `var/postgres-data`)
    - see documentation of [Installation Using Docker](docs/introduction/installation-using-docker.md) for details
- default parameters in `parameters.yml.dist` and `parameters_test.yml.dist` are for Docker installation (instead of native) (@MattCzerner)
- Front/NewsletterController: extracted duplicit rendering and add return typehints (@simara-svatopluk)
- Symfony updated to version 3.4 (@TomasLudvik)
    - autowiring is now done via Symfony PSR-4
    - services now use FQN as naming convention
    - services are private by default
    - inlined services (called via container) are set to public
    - services required by another service are defined in services.yml (e.g. Shopsys\ShopBundle\Model\Administrator\Security\AdministratorUserProvider: ~)
    - all inline calls of services changed to use FQN
    - services no longer required in services.yml have been removed
    - services instanced after DI container creation are set as synthetic
- users and administrators are logged out of all the sessions except the current one on password change (this is required in Symfony 4) (@TomasLudvik)
- running Phing without parameter now shows list of available targets instead of building application (@TomasLudvik)
- updated presta/sitemap-bundle to version 1.5.2 in order to avoid deprecated calls (@TomasLudvik)
 - updated SitemapListener to avoid using of deprecated SitemapListenerInterface
 

### Fixed
- `BrandFacade::create()` now generates friendly URL for all domains (@sspooky13)
- `Admin/HeurekaController::embedWidgetAction()` moved to new `Front/HeurekaController` as the action is called in FE template (@vitek-rostislav)
- PHPUnit tests do not fail on Windows machine with PHP 7.0 because of excessively long file paths (@PetrHeinz)
    - see [GitLab issue #21](https://git.shopsys-framework.com/shopsys/shopsys-framework/issues/21) for details 
- customizeBundle.js: on-submit actions are no longer triggered when form validation error occurs (@TomasLudvik)
- fixed google product feed availability values by updating it to v0.1.2 (@simara-svatopluk)
- reloading of order preview now calls `Shopsys.register.registerNewContent()` (@petr.kadlec)  
    - accepted [merge request](https://git.shopsys-framework.com/shopsys/shopsys-framework/merge_requests/27)
- CurrentPromoCodeFacace: promo code is not searched in database if code is empty (@petr.kadlec)
- CategoryRepository::getCategoriesWithVisibleChildren() checks visibility of children (@petr.kadlec)
- added missing migration for privacy policy article (@MattCzerner)

### Removed
- PHPStorm Inspect is no longer used for static analysis of source code (@TomasLudvik)
- Phing targets standards-ci and standards-ci-diff because they were redundant to standards and standards-diff targets (@TomasLudvik)
- deprecated packages `symplify/controller-autowire` and `symplify/default-autowire` (@TomasLudvik)

## [2.0.0-beta.20.0] - 2017-12-11
### Changed
- Docker `nginx.conf` has been upgraded with better performance settings (@TomasLudvik)
    - JavaScript and CSS files are compressed with GZip
    - static content has cache headers set in order to leverage browser cache
### Fixed
- miscellaneous annotations, typos and other minor fixes (@petr.kadlec)
    - accepted [merge request](https://git.shopsys-framework.com/shopsys/shopsys-framework/merge_requests/11)
- `CartController::addProductAction()`: now uses `Request` instance passed as the method argument (Symfony 3 style) instead of calling the base `Controller` method `getRequest()` (Symfony 2.x style) (@petr.kadlec)
    - see [Symfony upgrade log](https://github.com/symfony/symfony/blob/3.0/UPGRADE-3.0.md#frameworkbundle) for more information
    - accepted [merge request](https://git.shopsys-framework.com/shopsys/shopsys-framework/merge_requests/12)
- `ExecutionContextInterface::buildViolation()` (Symfony 3 style) is now used instead of `ExecutionContextInterface::addViolationAt()` (Symfony 2.x style) (@petr.kadlec)
    - see [Symfony upgrade log](https://github.com/symfony/symfony/blob/3.0/UPGRADE-3.0.md#validator) for more information
    - accepted [merge request](https://git.shopsys-framework.com/shopsys/shopsys-framework/merge_requests/13)

## [2.0.0-beta.19.2] - 2017-11-23
### Fixed
- updated symfony/symfony to v3.2.14 in order to avoid known security vulnerabilities (@TomasLudvik)

## [2.0.0-beta.19.1] - 2017-11-21
### Fixed
- coding standards check "phing standards" passes

## [2.0.0-beta.19.0] - 2017-11-21
### Added
- size of performance data fixtures and limits for performance testing are now configurable via parameters defined in [`parameters_common.yml`](app/config/parameters_common.yml) (@PetrHeinz)
- performance tests report database query counts (@PetrHeinz)
- UserDataFixture: alias for SettingValueDataFixture to fix [PHP bug #66862](https://bugs.php.net/bug.php?id=66862)

### Changed
- parameters that are in `parameters.yml` or `parameters_test.yml` that are not in their `.dist` templates are not removed during `composer install` anymore (@PetrHeinz)
- customer creating controllers are not catching exception for duplicate email, it is not necessary since it is done by UniqueEmail constraint now (@MattCzerner)
- input "remember me" in login form is encapsulated by its label for better UX

## [2.0.0-beta.18.0] - 2017-10-19
### Added
- [coding standards documentation](docs/contributing/coding-standards.md) (@vitek-rostislav)
- acceptance tests asserting successful image upload in admin for product, transport and payment (@vitek-rostislav)
- Docker based server stack for easier installation and development (@TomasLudvik)
    - see [Docker Installation Guide](docs/introduction/installation-using-docker.md) for details
- plugins can now extend the CRUD of categories (using `CategoryFormType`) (@MattCzerner)

### Changed
- cache deletion before running unit tests is now done using `Symfony\Filesystem` instead of using console command (@TomasLudvik)
    - deleting via console command `cache:clear` is slow, because it creates whole application container first and then deletes all cache created in process
- Windows locales list: use more tolerant name for Czech locale (@vitek-rostislav)
    - in Windows 2017 Fall Creators Update the locale name was changed from "Czech_Czech Republic" to "Czech_Czechia"
    - name "Czech" is acceptable in all Windows versions
- interfaces for CRON modules moved to [shopsys/plugin-interface](https://github.com/shopsys/plugin-interface) (@MattCzerner)
- `ImageDemoCommand` now prompts to truncate "images" db table when it is not empty before new demo images are loaded (@vitek-rostislav)

### Deleted
- logic of Heureka categorization moved to [shopsys/product-feed-heureka](https://github.com/shopsys/product-feed-heureka) (@MattCzerner)
    - all your current Heureka category data will be migrated into the new structure

### Fixed
- proper `baseUrl` value from `domains_urls.yaml` is now stored into `settings` when creating new domain (@vitek-rostislav)

## [2.0.0-beta.17.0] - 2017-10-03
### Added
- MIT license (@TomasLudvik)
- phing targets `eslint-check`, `eslint-check-diff`, `eslint-fix` and `eslint-fix-diff` to check and fix coding standards in JS files (@sspooky13)
    - executed as a part of targets `standards`, `standards-diff`, `standards-fix` and `standards-fix-diff`
- [product feed plugin for Google](https://github.com/shopsys/product-feed-google/) (@MattCzerner)
- new article explaining [Basics About Package Architecture](docs/introduction/basics-about-package-architecture.md) (@vitek-rostislav)

### Changed
- [`StandardFeedItemRepository`](src/Shopsys/ShopBundle/Model/Feed/Standard/StandardFeedItemRepository.php): now selects available products instead of sellable, filtering of not sellable products is made in product plugins (@MattCzerner)
- implementations of `StandardFeedItemInterface` now must have implemented methods `isSellingDenied()` and `getCurrencyCode()`(@MattCzerner)
- implementations of `FeedConfigInterface` now must have implemented method `getAdditionalInformation()` (@MattCzerner)

## [2.0.0-beta.16.0] - 2017-09-19
### Added
- new command `shopsys:plugin-data-fixtures:load` for loading demo data from plugins (@MattCzerner)
    - called during build of demo database
- new documentation about Shopsys Framework model architecture (@TomasLudvik)
- `FeedItemRepositoryInterface` (@vitek-rostislav)
    - moved from [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/)
- [template for github pull requests](docs/PULL_REQUEST_TEMPLATE.md) (@vitek-rostislav)

### Changed
- dependency [shopsys/plugin-interface](https://github.com/shopsys/plugin-interface/) upgraded from 0.1.0 to 0.2.0 (@MattCzerner)
- dependency [shopsys/product-feed-heureka](https://github.com/shopsys/product-feed-heureka/) upgraded from 0.2.0 to 0.4.0 (@MattCzerner)
- dependency [shopsys/product-feed-zbozi](https://github.com/shopsys/product-feed-zbozi/) upgraded from 0.2.0 to 0.4.0 (@MattCzerner)
- dependency [shopsys/product-feed-heureka-delivery](https://github.com/shopsys/product-feed-heureka-delivery/) upgraded from 0.1.1 to 0.2.0 (@vitek-rostislav)
- dependency [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/) upgraded from 0.2.1 to 0.3.0 (@vitek-rostislav)
- it is no longer needed to redeclare feed plugin's implementations of `FeedConfigInterface` in `services.yml` (@vitek-rostislav)
    - decision about providing proper instance of `FeedItemRepositoryInterface` is made in `FeedConfigFacade`
- FeedConfigRepository renamed to [`FeedConfigRegistry`](src/Shopsys/ShopBundle/Model/Feed/FeedConfigRegistry.php) (@MattCzerner)
    - it is not fetching data from Doctrine as other repositories, it only serves as a container for registering services of specific type
    - similar to [`PluginDataFixtureRegistry`](src/Shopsys/ShopBundle/Component/Plugin/PluginDataFixtureRegistry.php) or [`PluginCrudExtensionRegistry`](src/Shopsys/ShopBundle/Component/Plugin/PluginCrudExtensionRegistry.php)
- `UknownPluginDataFixtureException` renamed to [`UnknownPluginCrudExtensionTypeException`](src/Shopsys/ShopBundle/Component/Plugin/Exception/UnknownPluginCrudExtensionTypeException.php) because of a typo (@MattCzerner)
- [`FeedConfigRegistry`](src/Shopsys/ShopBundle/Model/Feed/FeedConfigRegistry.php) now contains all FeedConfigs in one array (indexed by type) (@vitek-rostislav)
    - definition and assertion of known feed configs types moved from [`RegisterProductFeedConfigsCompilerPass`](src/Shopsys/ShopBundle/DependencyInjection/Compiler/RegisterProductFeedConfigsCompilerPass.php) to [`FeedConfigRegistry`](src/Shopsys/ShopBundle/Model/Feed/FeedConfigRegistry.php)
    - changed message and arguments of [`UnknownFeedConfigTypeException`](src/Shopsys/ShopBundle/Model/Feed/Exception/UnknownFeedConfigTypeException.php)
- renamed methods working with standard feeds only to be more expressive (@PetrHeinz)
    - renamed `FeedConfigFacade::getFeedConfigs()` to `getStandardFeedConfigs()`
    - renamed `FeedFacade::generateFeedsIteratively()` to `generateStandardFeedsIteratively()`
    - renamed `FeedGenerationConfigFactory::createAll()` to `createAllForStandardFeeds()`
- [`parameters.yml.dist`](app/config/parameters.yml.dist): renamed parameter `email_for_error_reporting` to `error_reporting_email_to` (@vitek-rostislav)
- sender email for error reporting is now configured in [`parameters.yml.dist`](app/config/parameters.yml.dist) (@vitek-rostislav)
- reimplemented [`CategoriesType`](src/Shopsys/ShopBundle/Form/CategoriesType.php) (@Petr Heinz)
    - it now extends `CollectionType` instead of `ChoiceType`
    - it loads only those categories that are needed to show all selected categories in a tree, not all of them
    - collapsed categories can be loaded via AJAX
- [`CategoryRepository::findById()`](src/Shopsys/ShopBundle/Model/Category/CategoryRepository.php) now uses `find()` method of Doctrine repository instead of query builder so it can use cached results (@PetrHeinz)
- it is possible to mention occurrences of an image size in [`images.yml`](src/Shopsys/ShopBundle/Resources/config/images.yml) (@PetrHeinz)
    - previously they were directly in [`ImageController`](src/Shopsys/ShopBundle/Controller/Admin/ImageController.php)
    - they are not translatable anymore (too hard to maintain)

### Removed
- email for error reporting removed from [`parameters_test.yml.dist`](app/config/parameters_test.yml.dist) (@vitek-rostislav)
- removed unused private properties from classes (@PetrHeinz)
- removed `CategoriesTypeTransformerFactory` (@PetrHeinz)
    - the `CategoriesTypeTransformer` can be fully autowired after deletion of `$domainId`

### Fixed
- [`InlineEditPage::createNewRow()`](tests/ShopBundle/Acceptance/acceptance/PageObject/Admin/InlineEditPage.php) now waits for AJAX to complete (@PetrHeinz)
    - fixes false negatives of acceptance test [`PromoCodeInlineEditCest::testPromoCodeCreate()`](tests/ShopBundle/Acceptance/acceptance/PromoCodeInlineEditCest.php)

## 2.0.0-beta.15.0 - 2017-08-31
- previous beta versions released only internally (mentioned changes since 1.0.0)

### Added
- PHP 7 support
- [a basic knowledgebase](docs/index.md)
    - installation guide
    - guidelines for contributions
    - cookbooks
    - articles on automated testing

### Changed
- update to Symfony 3
- PSR-2 compliance
- English as a main language
    - language of first front-end domain
    - language of administration
    - all translatable message sources in English

### Deleted
- separation of HTTP smoke test module into a component:
    - https://github.com/shopsys/http-smoke-testing/
- separation of product feed modules into plugins:
    - https://github.com/shopsys/plugin-interface/
    - https://github.com/shopsys/product-feed-interface/
    - https://github.com/shopsys/product-feed-heureka/
    - https://github.com/shopsys/product-feed-heureka-delivery/
    - https://github.com/shopsys/product-feed-zbozi/

## 1.0.0 - 2016-11-09
- developed since 2014-03-31
- used only as internal platform for e-commerce projects of Shopsys Agency
- released only internally

### Added
- product catalogue
- registered customers
- basic orders management
- back-end administration
- front-end fulltext search
- front-end product filtering
- 3-step ordering process
- products variants
- simple promo codes
- product feeds for product aggregators
- basic CMS
- multiple administrators
- support for several currencies
- support for several languages
- support for several domains
- full friendly URL for main entities
- customizable SEO attributes for main entities

[Unreleased]: https://git.shopsys-framework.com/shopsys/shopsys-framework/compare/v2.0.0-beta.20.0..HEAD
[2.0.0-beta.20.0]: https://git.shopsys-framework.com/shopsys/shopsys-framework/compare/v2.0.0-beta.19.2...v2.0.0-beta.20.0
[2.0.0-beta.19.2]: https://git.shopsys-framework.com/shopsys/shopsys-framework/compare/v2.0.0-beta.19.1...v2.0.0-beta.19.2
[2.0.0-beta.19.1]: https://git.shopsys-framework.com/shopsys/shopsys-framework/compare/v2.0.0-beta.19.0...v2.0.0-beta.19.1
[2.0.0-beta.19.0]: https://git.shopsys-framework.com/shopsys/shopsys-framework/compare/v2.0.0-beta.18.0...v2.0.0-beta.19.0
[2.0.0-beta.18.0]: https://git.shopsys-framework.com/shopsys/shopsys-framework/compare/v2.0.0-beta.17.0...v2.0.0-beta.18.0
[2.0.0-beta.17.0]: https://git.shopsys-framework.com/shopsys/shopsys-framework/compare/v2.0.0-beta.16.0...v2.0.0-beta.17.0
[2.0.0-beta.16.0]: https://git.shopsys-framework.com/shopsys/shopsys-framework/compare/v2.0.0-beta.15.0...v2.0.0-beta.16.0 
