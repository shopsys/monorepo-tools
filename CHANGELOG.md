# Changelog
All notable changes, that change in some way the behavior of any of our packages that are maintained by monorepo repository.

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

Packages are formatted by release version. You can see all the changes done to package that you carry about with this tree.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### [shopsys/framework]
#### Added
- [#354 - Product search in elasticsearch](https://github.com/shopsys/shopsys/pull/354)
    - elasticsearch docker container: volumes are now set in order to preserve data when the container is shut down
    - added Symfony commands for creating and removing Elasticsearch indexes
    - added Symfony command for exporting all products to Elasticsearch
    - added new phing targets for working with Elasticsearch

#### Fixed
- [#260 - JS validation: dynamically added form inputs are now validated](https://github.com/shopsys/shopsys/pull/260)

## [7.0.0-alpha4] - 2018-08-02
### [shopsys/framework]
#### Added
- [#335 - Possibility to add a new administration page](https://github.com/shopsys/shopsys/pull/335)
    - added cookbook [Adding a New Administration Page](/docs/cookbook/adding-a-new-administration-page.md) along with the side menu and breadcrumbs

#### Changed
- [#302 - All persistent files like uploads are now stored using abstract filesystem (Flysystem)](https://github.com/shopsys/shopsys/pull/302)
    - abstract filesystem is used to store:
        - uploaded files and images
        - uploaded files and images via WYSIWYG
        - generated feeds
        - generated sitemaps
    - all services using PernamentPhpFileCache now use RedisCache instead
- [#286 - Instantiate entity data objects by factories](https://github.com/shopsys/shopsys/pull/286)
    - entity data objects have only an empty constructor now
    - creation of entity data objects moved to factories to allow extensibility
- [#244 Redesign of administration](https://github.com/shopsys/shopsys/pull/244)
    - full-width layout
    - colors changed to match new Shopsys CI
    - main menu moved to the left panel along with settings menu
    - menu items do not have type anymore, which lead to simplification of the code
    - menu was slightly restructured
- [#285 - Removal of base data fixtures](https://github.com/shopsys/shopsys/pull/285)
    - all Base Data Fixtures were removed
    - the data are created either in database migrations or in Demo Data Fixtures
- [#271 - Complete refactoring of feeds functionality](https://github.com/shopsys/shopsys/pull/271)
  - modules are responsible for querying the data to improve performance
  - interfaces from package product-feed-interface are not used anymore as they were only important with open-box architecture
  - only relevant data is fetched from the database, should result in enhanced performance
  - FeedInterface and FeedInfoInterface define the way feeds are registered in the system
  - FeedExport is responsible for the actual generation of a file in batches on a specific domain
  - FeedRenderer is responsible for rendering the feed from Twig template
  - FeedPathProvider is responsible for providing the correct filepath and url to the specified feed on a domain
  - ProductUrlsBatchLoader and ProductParametersBatchLoader are responsible for loading product data in batches 
  - cron modules use the logger for debug information
  - DailyFeedCronModule is responsible for continuation of the correct feed after waking up
- [#182 - Cart: flush() is called only if there are really some changes in cart items](https://github.com/shopsys/shopsys/pull/182)
- admin menu is now implemented using the KnpMenuBundle as a part of [#335 - Possibility to add a new administration page](https://github.com/shopsys/shopsys/pull/335)
    - old implementation using the `admin_menu.yml` configuration along with `AdminMenuYamlFileExtractor` was removed
    - class `Breadcrumb` was renamed to `BreadcrumbOverrider` and its scope was reduced
- [#313 - Streamed logging](https://github.com/shopsys/shopsys/pull/313)
    - monolog logs into streams instead of files (use `docker-compose logs` to access it)
    - see details in the [Logging](/docs/introduction/logging.md) article
- [#341 - Category entity in constructor of CategoryRepository is resolved via EntityNameResolver](https://github.com/shopsys/shopsys/pull/341)
- [#364 - Admin: brand form is rendered via BrandFormType](https://github.com/shopsys/shopsys/pull/364)
- [#370 - MultidomainEntityClassFinderFacade: metadata are checked on class name resolved by EntityNameResolver](https://github.com/shopsys/shopsys/pull/370)

#### Fixed
- [#304 - Unnecessary SQL queries on category detail in admin](https://github.com/shopsys/shopsys/pull/304):
    - category translations for ancestor category are loaded in the same query as categories
- [#317 - Travis build is failing for shopsys/framework](https://github.com/shopsys/shopsys/pull/317):
    - framework package requires redis bundle and redis extension
    - redis extension enabled in configuration for travis
- [#316 - Admin: feed items on feeds generation page contain clickable link and datetime](https://github.com/shopsys/shopsys/pull/316)
    - checks for existing file and for modified time of file use abstract filesystem methods
- [#314 - Dropped triggers before creation](https://github.com/shopsys/shopsys/pull/314)
- [#263 - CartWatcherFacade: fixed swapped messages](https://github.com/shopsys/shopsys/pull/263)
- [#339 - Downgrade snc/redis-bundle to 2.1.4 due to Issue in phpredis](https://github.com/shopsys/shopsys/pull/339)
- [#351 - added missing typehints in methods of CookiesFacade and OrderMailService](https://github.com/shopsys/shopsys/pull/351)
- [#352 - flushes executed in loops are now executed outside of loop](https://github.com/shopsys/shopsys/pull/352)
- [#342 - procedures are now executed only if relevant columns are changed](https://github.com/shopsys/shopsys/pull/342)
- [#362 - guidelines-for-pull-request.md: fixed indentation of lines and code blocks](https://github.com/shopsys/shopsys/pull/362)
- [#372 - test fails if framework is set as singledomain](https://github.com/shopsys/shopsys/pull/372)

#### Removed
- Error reporting functionality was removed as a part of [#313 - Streamed logging](https://github.com/shopsys/shopsys/pull/313)
    - error reporting should be done from the outside of the application (eg. by [Kubernetes](https://kubernetes.io/))

### [shopsys/project-base]
#### Fixed
- [#347 - Composer: disable installation of broken version of codeception/stub](https://github.com/shopsys/shopsys/pull/347)
- [#353 - Fixed paths in project-base/docker/conf/docker-compose-win.yml.dist](https://github.com/shopsys/shopsys/pull/353)
- [#363 - docker-sync.yml added to gitignore to allow individual configuration](https://github.com/shopsys/shopsys/pull/363)

### [shopsys/shopsys]
#### Added
- [#320 - Docs: overview of possible and impossible glassbox customizations](https://github.com/shopsys/shopsys/pull/320)
    - added [framework-extensibility.md](/docs/introduction/framework-extensibility.md) article
#### Changed
- [#296 - normalize phing target "timezones-check"](https://github.com/shopsys/shopsys/pull/296): [@pk16011990]

### [shopsys/monorepo-tools]
#### Added
- [#345 - monorepo-tools: allow incremental build of monorepo](https://github.com/shopsys/shopsys/pull/345) [@lukaso]
- [#311 - monorepo split allows adding new package when monorepo is already tagged](https://github.com/shopsys/shopsys/pull/311)
#### Fixed
- [#281 - monorepo-tools: Fix scripts to work on OS X](https://github.com/shopsys/shopsys/pull/282) [@lukaso]

### [shopsys/coding-standards]
#### Added
- [#308 - Sniff for forgotten dumps](https://github.com/shopsys/shopsys/pull/308)
    - ecs tester for coding standards was added with tests for sniffs and fixers [@TomasVotruba]
    - added support for checking standards of file types twig, html
    - added sniff for checking of forgotten dump functions

### [shopsys/product-feed-google]
#### Changed
- [#286 - Instantiate entity data objects by factories](https://github.com/shopsys/shopsys/pull/286)
    - entity data objects have only an empty constructor now
    - creation of entity data objects moved to factories to allow extensibility
- [#271 - Complete refactoring of feeds functionality](https://github.com/shopsys/shopsys/pull/271)
    - for details see section shopsys/framework

#### Fixed
- [#323 - Packages that depend on shopsys/framework need redis extension enabled](https://github.com/shopsys/shopsys/pull/323)
    - redis extension in travis config was enabled

### [shopsys/product-feed-heureka]
#### Changed
- [#286 - Instantiate entity data objects by factories](https://github.com/shopsys/shopsys/pull/286)
    - entity data objects have only an empty constructor now
    - creation of entity data objects moved to factories to allow extensibility
- [#271 - Complete refactoring of feeds functionality](https://github.com/shopsys/shopsys/pull/271)
    - for details see section shopsys/framework

#### Fixed
- [#323 - Packages that depend on shopsys/framework need redis extension enabled](https://github.com/shopsys/shopsys/pull/323)
    - redis extension in travis config was enabled

### [shopsys/product-feed-heureka-delivery]
#### Changed
- [#271 - Complete refactoring of feeds functionality](https://github.com/shopsys/shopsys/pull/271)
    - for details see section shopsys/framework

#### Fixed
- [#323 - Packages that depend on shopsys/framework need redis extension enabled](https://github.com/shopsys/shopsys/pull/323)
    - redis extension in travis config was enabled

### [shopsys/product-feed-interface]
#### Abandoned
The package was removed from monorepo during [#271 - Complete refactoring of feeds functionality](https://github.com/shopsys/shopsys/pull/271) and it's development was discontinued.
It was only important with [the original open-box architecture](https://blog.shopsys.com/architecture-and-workflow-overview-f54ccae348ce), but after the creation of [shopsys/framework] there is no need for isolating interfaces in separate packages.

### [shopsys/product-feed-zbozi]
#### Changed
- [#286 - Instantiate entity data objects by factories](https://github.com/shopsys/shopsys/pull/286)
    - entity data objects have only an empty constructor now
    - creation of entity data objects moved to factories to allow extensibility
- [#271 - Complete refactoring of feeds functionality](https://github.com/shopsys/shopsys/pull/271)
    - for details see section shopsys/framework

#### Fixed
- [#323 - Packages that depend on shopsys/framework need redis extension enabled](https://github.com/shopsys/shopsys/pull/323)
    - redis extension in travis config was enabled
    
### [shopsys/project-base]
#### Added
- configuration for admin controllers as a part of [#335 - Possibility to add a new administration page](https://github.com/shopsys/shopsys/pull/335)
    - see the config file in `src/Shopsys/ShopBundle/Resources/config/routing.yml`

#### Fixed
- [#315 - Route logout/ without csrf token returns not found](https://github.com/shopsys/shopsys/pull/315)
    - route logout/ must to be called with token in every case because LogoutListener from Symfony throws exception if token generator is set in configuration of firewall but the route logout is used without csrf token parameter
- [#339 - Downgrade snc/redis-bundle to 2.1.4 due to Issue in phpredis](https://github.com/shopsys/shopsys/pull/339)

## [7.0.0-alpha3] - 2018-07-03
### [shopsys/framework]
#### Changed
- [#272 - Changed concept of Components](https://github.com/shopsys/shopsys/pull/143):
    - added definition of Components in [components.md](./docs/introduction/components.md):
    - by this definition, classes that did not match it were moved or refactored.
    - FriendlyUrlGenerator refactored: FriendlyUrlGeneratorFacade does not know anything about particular entities that the friendly urls are generated for.
        These data are now served from implementations of FriendlyUrlDataProviderInterface.
- [#143 - Shopsys framework now uses latest version of Shopsys coding standards](https://github.com/shopsys/shopsys/pull/143) [Shopsys Coding Standards dev-master](./packages/coding-standards) is now used
    - version of coding-standards package was updated to dev-master in [composer.json](./packages/framework/composer.json)
    - modified [travis script](./packages/framework/project-base/.travis.yml)
        - removed check for php 7.0
        - php-cs-fixer, phpcs, phpmd binaries replaced by ecs binaries
        - inline code skips moved to [autoload-easy-coding-standard.yml](./packages/framework/autoload-easy-coding-standard.yml) 
- [#171 - Update to twig 2.x](https://github.com/shopsys/shopsys/pull/171):
    - updated to Twig 2.4.8
    - all depracated calls has been fixed
- [#188 - Rebrading of Shopsys Framework](https://github.com/shopsys/shopsys/pull/188/)
    - all occurrences of netdevelo were changed to shopsys
- [#257 - Admin: reset.less: disable scrollbar normalization in order to fix problems with jQuery UI drag&drop]( https://github.com/shopsys/shopsys/pull/257)
    - dragged item is now at correct position
        -scrollbar normalization was disabled for sortable components 
- [#261 - Sending personal data to Heureka can be disabled](https://github.com/shopsys/shopsys/pull/261)
    - the last step of cart contains opt-out checkbox to disable sending personal data to Heureka (if Heureka Verified by Customers is enabled on the domain)
- [#206 clearing Setting's cache is now done via DoctrineEventListener](https://github.com/shopsys/shopsys/pull/206)
    - `EntityManagerFacade` was removed
    - Doctrine identity map can be cleared via `EntityManager::clear()` directly
- [#254 - Removal of EntityDetail classes](https://github.com/shopsys/shopsys/pull/276)
    - `TransportDetail` and `TransportDetailFactory` were removed - `TransportFacade` is now able to provide transport base prices
    - `PaymentDetail` and `PaymentDetailFactory` were removed - `PaymentFacade` is now able to provide payment base prices
    - `ProductDetail` and `ProductDetailFactory` were removed - new `ProductCachedAttributesFacade` is now responsible for caching of products selling prices and parameters 
    - `CategoryDetail` was renamed to `CategoryWithPreloadedChildren` and methods for it's creation were moved from `CategoryDetailFactory` to new `CategoryWithPreloadedChildrenFactory` 
    - `LazyLoadedCategoryDetail` was renamed to `CategoryWithLazyLoadedVisibleChildren` and methods for it's creation were moved from deleted `CategoryDetailFactory` to new `CategoryWithLazyLoadedVisibleChildrenFactory` 
- [#274 grid: drag&drop is supported with enabled gridInlineEdit](https://github.com/shopsys/shopsys/pull/274)
- [#165 Different approach to multidomain entities](https://github.com/shopsys/shopsys/pull/165)
    - multi-domain entities were changed so they are used similarly to translations
    - main entities have a bidirectional association to a collection of its entity domains (eg. `BrandDomain`)
        - only the main entity has access to its entity domains
        - multi-domain attributes are accessed via the main entity
    - the main entities are responsible for creating and editing its entity domains
        - entity domain factories such as `BrandDomainFactory` were removed
    - entity domains have their own IDs instead of compound primary keys
    - entities that were modified: `Brand`, `Product`, `Category`, `Payment` and `Transport`
    - `BrandEditFormType`, `BrandDetail` and `BrandDetailFactory` were removed as they were no longer necessary
    - `DomainsType` now uses array of booleans indexed by domain IDs instead of array of domain IDs to be consistent with the behavior of `MultidomainType`
    - `CategoryDomain::$hidden` was changed to `CategoryDomain::$enabled` in sake of consistency
    - `PaymentDomain` and `TransportDomain` are now created even for domains on which the entity should not be visible (to allow for other multi-domain entities and in the sake of consistency)

#### Fixed
- [#246 - docker-sync.yml.dist: fixed not existing relative paths](https://github.com/shopsys/shopsys/pull/246) [@DavidKuna]
- [#132 - Admin: brand edit page: URLs setting rendering](https://github.com/shopsys/shopsys/pull/132):
    - admin: brand detail page: rendering of URLs setting
        - brand creation: URLs setting is not rendered at all
        - brand editing: URLs section is rendered in the SEO section
- [#173 - remove editData from model: all editData from framework model were merged into Data](https://github.com/shopsys/shopsys/pull/173):
    - remove editData from model: all editData from framework model were merged into its Data relatives
        - merged model EditData into Data with its Factory and modified Facade and Controller for
                - Product
                - Payment
                - Transport
                - Brand
- [#176 - Admin: Validation for free shipping is inconsistent](https://github.com/shopsys/shopsys/pull/176)
    - `Resources/views/Admin/Content/TransportAndPayment/freeTransportAndPaymentLimitSetting.html.twig`: `form_errors` was included for `form_widget` for consistency of admin forms
- [#228 - show selectbox options](https://github.com/shopsys/shopsys/pull/228)
    - the use of jQuery plugin for selectboxes was modified on Shopsys Framework side so options will now be seen 
- [#243 - Admin: changed domain icon in e-shop domain administration can be saved](https://github.com/shopsys/shopsys/pull/243)
    - changed domain icon in e-shop domain administration can be saved
- copying of localized entities
    - detection of new locale is now done before multidomain data are created 

### [shopsys/project-base]
#### Changed
- [#143 - Shopsys framework now uses latest version of Shopsys coding standards](https://github.com/shopsys/shopsys/pull/143) [Shopsys Coding Standards dev-master](./packages/coding-standards/) is now used
    - version of coding-standards package was updated to dev-master in [composer.json](./packages/project-base/composer.json)
    - inline code skips moved to [autoload-easy-coding-standard.yml](./project-base/autoload-easy-coding-standard.yml)
    - replaced php-cs-fixer. phpcs. phpmd for ecs in [build.xml](./project-base/build.xml) and [build-dev.xml](./project-base/build-dev.xml) scripts
- [#171 - Update to twig 2.x](https://github.com/shopsys/shopsys/pull/171):
    - updated to Twig 2.4.8
    - all depracated calls has been fixed
- [#188 - Rebrading of Shopsys Framework](https://github.com/shopsys/shopsys/pull/188/)
    - old logo was changed for the new Shopsys Framework logo
    - all occurrences of netdevelo were changed to shopsys
- [#165 Different approach to multidomain entities](https://github.com/shopsys/shopsys/pull/165)
    - multi-domain attributes are accessed via their main entities (instead of usual entity details)

#### Fixed
- [#131 - correct rendering of checkbox label](https://github.com/shopsys/shopsys/pull/131):
    - `Front/Form/theme.html.twig`: block `checkbox_row` now uses block `form_label` for proper label rendering
        - the absence of `label` html tag was causing problems with JS validation (the error message was not included in the popup overview)
- [#229 - php-fpm/Dockerfile: switch to another mirror of alpine linux repository](https://github.com/shopsys/shopsys/pull/229):
    - fix uninstallable postgres 9.5 by using repository https://dl-cdn.alpinelinux.org/alpine/ instead of https://dl-3.alpinelinux.org/alpine/
- [#242 - php-fpm/Dockerfile: drop usage of https when accessing dl-cdn.alpinelinux.org](https://github.com/shopsys/shopsys/pull/242)
- [#277 - Tests fail when only one domain is set](https://github.com/shopsys/shopsys/issues/277)

#### Security
- [#178 - JsFormValidatorBundle security issue with Ajax validation](https://github.com/shopsys/shopsys/pull/178)
    - removed the bundle's public route that allowed lookup any DB table by any field
    - the purpose of the route is for ajax validation of an entity uniqueness but the feature is not used anyway  

### [shopsys/shopsys]
#### Added
- [#143](https://github.com/shopsys/shopsys/pull/143) [Shopsys Coding Standards dev-master](./packages/coding-standards/) is now used
    - config [easy-coding-standard.yml](./easy-coding-standard.yml]) for importing rules of new easy-coding-standard package

#### Changed
- [#143](https://github.com/shopsys/shopsys/pull/143) [Shopsys Coding Standards dev-master](./packages/coding-standards/) is now used
    - php-cs-fixer, phpcs, phpmd binaries replaced by ecs binaries
    - build scripts were modified to work with new easy-coding-standard checker 
- [#230 - composer-dev updates dependencies if composer.json was changed](https://github.com/shopsys/shopsys/pull/230)

#### Fixed
- [#266 ecs fix and unification in monorepo](https://github.com/shopsys/shopsys/pull/266)
    - ObjectIsCreatedByFactorySniff: cover edge case
        - previous implementation failed eg. when creating a class using a variable (new $className;)
   - autoload-easy-coding-standard.yml renamed to easy-coding-standard.yml as it is not autoloaded in any way
   - all phing targets excluding *-diff use --clear-cache option
   - all packages use their own configuration file
   - all packeges skip ObjectIsCreatedByFactorySniff in tests folder

### [shopsys/coding-standards]
#### Added
- [#249 - First architectonical codesniff](https://github.com/shopsys/shopsys/pull/249)
    - new sniff `ObjectIsCreatedByFactorySniff` was created and was integrated into coding standards as service

#### Changed
- [#143](https://github.com/shopsys/shopsys/pull/143) [EasyCodingStandard v4.3.0](https://github.com/Symplify/EasyCodingStandard/tree/4.3) is now used
    - rules config file changed its format from neon to yaml

#### Fixed
- [#222 - coding-standards package is now up to date with new PHP_codesniffer v3.3.0](https://github.com/shopsys/shopsys/pull/222)
    - import of parent class of ForbiddenExitSniff was corrected

### [shopsys/form-types-bundle]
#### Changed
- [#143 - Shopsys framework now uses latest version of Shopsys coding standards](https://github.com/shopsys/shopsys/pull/143) [Shopsys Coding Standards dev-master](./packages/coding-standards/) is now used
    - version of coding-standards package was updated to dev-master in [composer.json](./packages/form-types-bundle/composer.json)
    - modified [travis script](./packages/form-types-bundle/.travis.yml)
        - removed check for php 7.0 due to compatibility with ecs
        - php-cs-fixer, phpcs, phpmd binaries replaced by ecs binary

### [shopsys/migrations]
#### Changed
- [#143 - Shopsys framework now uses latest version of Shopsys coding standards](https://github.com/shopsys/shopsys/pull/143) [Shopsys Coding Standards dev-master](./packages/coding-standards/) is now used
    - version of coding-standards package was updated to dev-master in [composer.json](./packages/migrations/composer.json)
    - modified [travis script](./packages/migrations/.travis.yml)
        - removed check for php 7.0 due to compatibility with ecs
        - php-cs-fixer, phpcs, phpmd binaries replaced by ecs binary
- [#185 Migrations now can be installed in different order or even be skipped](https://github.com/shopsys/shopsys/pull/185)
    - order of installed migration is saved in migrations-lock.yml
        - this order can be changed
        - migrations can be marked as skipped
    - you can read about the details in the [documentation](./docs/introduction/database-migrations.md)

### [shopsys/plugin-interface]
#### Changed
- [#143 - Shopsys framework now uses latest version of Shopsys coding standards](https://github.com/shopsys/shopsys/pull/143) [Shopsys Coding Standards dev-master](./packages/coding-standards/) is now used
    - version of coding-standards package was updated to dev-master in [composer.json](./packages/plugin-interface/composer.json)
    - modified [travis script](./packages/plugin-interface/.travis.yml)
        - removed check for php 7.0 due to compatibility with ecs
        - php-cs-fixer, phpcs, phpmd binaries replaced by ecs binary

### [shopsys/product-feed-google]
#### Changed
- [#143 - Shopsys framework now uses latest version of Shopsys coding standards](https://github.com/shopsys/shopsys/pull/143) [Shopsys Coding Standards dev-master](./packages/coding-standards/) is now used
    - version of coding-standards package was updated to dev-master in [composer.json](./packages/product-feed-google/composer.json)
    - modified [travis script](./packages/product-feed-google/.travis.yml)
        - removed check for php 7.0 due to compatibility with ecs
        - php-cs-fixer, phpcs, phpmd binaries replaced by ecs binary
- [#171 - Update to twig 2.x](https://github.com/shopsys/shopsys/pull/171):
    - updated to Twig 2.4.8
    - all depracated calls has been fixed

### [shopsys/product-feed-heureka]
#### Changed
- [#143 - Shopsys framework now uses latest version of Shopsys coding standards](https://github.com/shopsys/shopsys/pull/143) [Shopsys Coding Standards dev-master](./packages/coding-standards/) is now used
    - version of coding-standards package was updated to dev-master in [composer.json](./packages/product-feed-heureka/composer.json)
    - inline code skips moved to [autoload-easy-coding-standard.yml](./packages/product-feed-heureka/autoload-easy-coding-standard.yml) 
    - modified [travis script](./packages/product-feed-heureka/.travis.yml)
        - removed check for php 7.0 due to compatibility with ecs
        - php-cs-fixer, phpcs, phpmd binaries replaced by ecs binary
- [#171 - Update to twig 2.x](https://github.com/shopsys/shopsys/pull/171):
    - updated to Twig 2.4.8
    - all depracated calls has been fixed

### [shopsys/product-feed-heureka-delivery]
#### Changed
- [#143 - Shopsys framework now uses latest version of Shopsys coding standards](https://github.com/shopsys/shopsys/pull/143) [Shopsys Coding Standards dev-master](./packages/coding-standards/) is now used
    - version of coding-standards package was updated to dev-master in [composer.json](./packages/product-feed-heureka-delivery/composer.json)
    - modified [travis script](./packages/product-feed-heureka-delivery/.travis.yml)
        - removed check for php 7.0 due to compatibility with ecs
        - php-cs-fixer, phpcs, phpmd binaries replaced by ecs binary
- [#171 - Update to twig 2.x](https://github.com/shopsys/shopsys/pull/171):
    - updated to Twig 2.4.8
    - all depracated calls has been fixed

### [shopsys/product-feed-interface]
#### Changed
- [#143 - Shopsys framework now uses latest version of Shopsys coding standards](https://github.com/shopsys/shopsys/pull/143) [Shopsys Coding Standards dev-master](./packages/coding-standards/) is now used
    - version of coding-standards package was updated to dev-master in [composer.json](./packages/product-feed-interface/composer.json)
    - modified [travis script](./packages/product-feed-interface/.travis.yml)
        - removed check for php 7.0 due to compatibility with ecs
        - php-cs-fixer, phpcs, phpmd binaries replaced by ecs binary

### [shopsys/product-feed-zbozi]
#### Changed
- [#143 - Shopsys framework now uses latest version of Shopsys coding standards](https://github.com/shopsys/shopsys/pull/143) [Shopsys Coding Standards dev-master](./packages/coding-standards/) is now used
    - version of coding-standards package was updated to dev-master in [composer.json](./packages/product-feed-zbozi/composer.json)
    - inline code skips moved to [autoload-easy-coding-standard.yml](./packages/product-feed-zbozi/autoload-easy-coding-standard.yml) 
    - modified [travis script](./packages/product-feed-zbozi/.travis.yml)
        - removed check for php 7.0 due to compatibility with ecs
        - php-cs-fixer, phpcs, phpmd binaries replaced by ecs binary
- [#171 - Update to twig 2.x](https://github.com/shopsys/shopsys/pull/171):
    - updated to Twig 2.4.8
    - all depracated calls has been fixed

## [7.0.0-alpha2] - 2018-05-24
### [shopsys/framework]
#### Added
- [#74 - Export personal information](https://github.com/shopsys/shopsys/pull/74):
    - Countries have code in ISO 3166-1 alpha-2 
    - admin: added site content and email template for personal data export 
- [#95 - Entity name resolving in EntityManager, QueryBuilders and Repositories](https://github.com/shopsys/shopsys/pull/95):
    - extended glass-box model entities are now used instead of their parent entities in EntityManager and QueryBuilders 
        - this removes the need to manually override all repositories that work with extended entities
        - the functionality is automatically tested in [shopsys/project-base](https://github.com/shopsys/project-base)
            - see `\Tests\ShopBundle\Database\EntityExtension\EntityExtensionTest`
- [#107 - Entities by factories](https://github.com/shopsys/shopsys/pull/107):
    - entities are created by factories 
        - allowing override factory that creates extended entities in project-base

#### Changed
- [#102 - Protected visibility of all private properties and methods of facades](https://github.com/shopsys/shopsys/pull/102):
    - visibility of all private properties and methods of repositories of entities was changed to protected 
        - there are changed only repositories of entities because currently there was no need for extendibility of other repositories
        - protected visibility allows overriding of behavior from projects
- [#116 - Visibility of properties and methods of DataFactories and Repositories is protected](https://github.com/shopsys/shopsys/pull/116)
    - visibility of all private properties and methods of DataFactories was changed to protected 
        - protected visibility allows overriding of behavior from projects
- [#113 - terminology: expression "indexes" is used now instead of "indices"](https://github.com/shopsys/shopsys/pull/113)
    - unification of terminology - indices and indexes 
        - there is only "indexes" expression used now
- [#103 - Defaultly rendered form types](https://github.com/shopsys/shopsys/pull/103):
    - `CustomerFormType`, `PaymentFormType` and `TransportFormType` are now all rendered using FormType classes and they
        are ready for extension from `project-base` side.
- [#70 - extraction of project-independent part of Shopsys\Environment](https://github.com/shopsys/shopsys/pull/70):
    - moved constants with types of environment from [shopsys/project-base](https://github.com/shopsys/project-base) 
        - moved from `\Shopsys\Environment` to `\Shopsys\FrameworkBundle\Component\Environment\EnvironmentType`
- [#87 - service deprecations](https://github.com/shopsys/shopsys/pull/87):
    - service definition follows Symfony 4 autowiring standards (@EdoBarnas)
        - FQN is always used as service ID
    - usage of interfaces is preferred, if possible
    - all services are explicitly defined
        - services with common suffixes (`*Facade`, `*Repository` etc.) are auto-discovered
        - see `services.yml` for details
- [#91 - all exception interfaces are now Throwable](https://github.com/shopsys/shopsys/pull/91):
    - all exception interfaces are now Throwable 
    - visibility of all private properties and methods of facades was changed to protected 
        - protected visibility allows overriding of behavior from projects
- [#130 - License condition for turnover changed from 12 to 3 months](https://github.com/shopsys/shopsys/pull/130)

#### Fixed
- [#89 - choiceList values are prepared for js ChoiceToBooleanArrayTransformer](https://github.com/shopsys/shopsys/pull/89) 
    - choiceList values are prepared for js Choice(s)ToBooleanArrayTransformer 
        - fixed "The choices were not found" console js error in the params filter
- [relevant CHANGELOG.md files updated](https://github.com/shopsys/shopsys/commit/68d730ac9eed9f8cf29c843f89718194ad51b1da):
    - command `shopsys:server:run` for running PHP built-in web server for a chosen domain
- [#108 - demo entity extension](https://github.com/shopsys/shopsys/pull/108)
    - db indices for product name are now created for translations in all locales 
    - `LoadDataFixturesCommand` - fixed the `--fixtures` option description

### [shopsys/project-base]
#### Added
- [#74 - Export personal information](https://github.com/shopsys/shopsys/pull/74): 
    - frontend: added site for requesting personal data export [@stanoMilan]
- [#94 - Installation guide update](https://github.com/shopsys/shopsys/pull/94): 
    - support for [native installation](https://github.com/shopsys/shopsys/blob/master/docs/installation/native-installation.md) of the application

#### Changed
- [#70 - extraction of project-independent part of Shopsys\Environment](https://github.com/shopsys/shopsys/pull/70):
    - moved constants with types of environment into [shopsys/framework](https://github.com/shopsys/framework)
    - moved from `\Shopsys\Environment` to `\Shopsys\FrameworkBundle\Component\Environment\EnvironmentType`
- [Dependency Injection strict mode is now enabled](https://github.com/shopsys/shopsys/commit/cdcb51268d56770ae460fe22b41cc09f51c4aab6) [@EdoBarnas]: 
    - disables autowiring features that were removed in Symfony 4

#### Fixed
- [#92 - swiftmailer setting delivery_address renamed to delivery_addresses](https://github.com/shopsys/shopsys/pull/92):
    - swiftmailer setting `delivery_address` renamed to `delivery_addresses` as the former does not exist anymore in version 3.*
        - see https://github.com/symfony/swiftmailer-bundle/commit/5edfbd39eaefb176922a346c16b0ae3aaeec87e0
        - the new setting requires array instead of string so the parameter `mailer_master_email_address` is wrapped into array in config
- [`FpJsFormValidator` error in console on FE order pages](https://github.com/shopsys/shopsys/commit/fbadde0966e92941dd470591d6a8a4924a798aa8)
- [failure during Docker image build triggered by `E: Unable to locate package postgresql-client-9.5`](https://github.com/shopsys/shopsys/pull/110) 

#### Removed
- [#94 - Installation guide update](https://github.com/shopsys/shopsys/pull/94): 
    - support of installation using Docker for Windows 10 Home and lower
    - virtualization is extremely slow, native installation has much better results in such case

### [shopsys/shopsys]
#### Added
- [#108 - demo entity extension](https://github.com/shopsys/shopsys/pull/108): 
    - [cookbook](docs/cookbook/adding-new-attribute-to-an-entity.md) for adding new attribute to an entity

#### Changed
- [#128 - CHANGELOG.md new format](https://github.com/shopsys/shopsys/pull/128)
- [#110 - PHP-FPM Docker image tweaked for easier usage](https://github.com/shopsys/shopsys/pull/110):
    - PHP-FPM Docker image tweaked for easier usage
    - switched to Docker image `php:7.2-fpm-alpine` instead of `phpdockerio/php72-fpm:latest`
            - official PHP Docker image is much more stable and provides tags other than `latest`
            - built on Alpine linux which uses `apk` instead of `apt-get`
            - in the container there is no `bash` installed, use `sh` instead
    - all installation guides verified and tweaked
        - Docker installation supported on Linux, MacOS and Windows 10 Pro and higher (recommended way of installing the application)
        - native installation is also supported (recommended on Windows 10 Home and lower)
    - as a rule, using minor versions of docker images (eg. `1.2` or `1.2-alpine`) if possible
    - docs and `docker-compose.yml` templates reflect [changes of Docker images in shopsys/project-base]
    - `docker-compose-win.yml.dist` created for Windows OS which creates local volume because of permission problems with
        `postgresql` mounting
    - docs: changed `./phing` instruction code with `php phing` to make it work with all operating systems

#### Fixed
- [#117 - documentation: missing redis extension in required php extensions](https://github.com/shopsys/shopsys/pull/117) [@pk16011990]
- [#124 - Admin: Customer cannot be saved + fixed js error from administration console](https://github.com/shopsys/shopsys/pull/124): 
    - admin: e-mail validation in customer editation is working correctly now

### [shopsys/http-smoke-testing]
#### Added
- [Troubleshooting section in `README.md` with explanation why tests do not fail on non-existing routes](https://github.com/shopsys/http-smoke-testing/commit/8f700eda96c2f6e1b018e56f5b03a46d09b4ae00)

### [shopsys/product-feed-google]
#### Changed
- [#116 - Visibility of properties and methods of DataFactories and Repositories is protected](https://github.com/shopsys/shopsys/pull/116):
    - visibility of all private properties and methods of repositories of entities was changed to protected
        - there are changed only repositories of entities because currently there was no need for extendibility of other repositories
        - protected visibility allows overriding of behavior from projects
- [Doctrine entities are used for storing data instead of using `DataStorageProviderInterface`](https://github.com/shopsys/shopsys/commit/3f32f513276f112d8ef4bdf854e413829bcf80f8)
    - previously saved data will be migrated
- [#102 - Protected visibility of all private properties and methods of facades](https://github.com/shopsys/shopsys/pull/102):
    - visibility of all private properties and methods of facades was changed to protected
        - protected visibility allows overriding of behavior from projects

### [shopsys/product-feed-heureka] 
#### Changed 
- [#116 - Visibility of properties and methods of DataFactories and Repositories is protected](https://github.com/shopsys/shopsys/pull/116): 
    - visibility of all private properties and methods of repositories of entities was changed to protected 
        - there are changed only repositories of entities because currently there was no need for extendibility of other repositories 
        - protected visibility allows overriding of behavior from projects 
- [Doctrine entities are used for storing data instead of using `DataStorageProviderInterface`](https://github.com/shopsys/shopsys/commit/3f32f513276f112d8ef4bdf854e413829bcf80f8) 
    - previously saved data will be migrated 
- [#102 - Protected visibility of all private properties and methods of facades](https://github.com/shopsys/shopsys/pull/102): 
    - visibility of all private properties and methods of facades was changed to protected 
        - protected visibility allows overriding of behavior from projects 

### [shopsys/product-feed-zbozi]
#### Changed
- [#116 - Visibility of properties and methods of DataFactories and Repositories is protected](https://github.com/shopsys/shopsys/pull/116):
    - visibility of all private properties and methods of repositories of entities was changed to protected
        - there are changed only repositories of entities because currently there was no need for extendibility of other repositories
        - protected visibility allows overriding of behavior from projects
- [Doctrine entities are used for storing data instead of using `DataStorageProviderInterface`](https://github.com/shopsys/shopsys/commit/3f32f513276f112d8ef4bdf854e413829bcf80f8)
    - previously saved data will be migrated
- [#102 - Protected visibility of all private properties and methods of facades](https://github.com/shopsys/shopsys/pull/102):
    - visibility of all private properties and methods of facades was changed to protected
        - protected visibility allows overriding of behavior from projects

## 7.0.0-alpha1 - 2018-04-12
- We are releasing version 7 (open-source project known as Shopsys Framework) to better distinguish it from Shopsys 6
  (internal platform of Shopsys company) and older versions that we have been developing and improving for 15 years.

### [shopsys/framework]
#### Added
- extracted core functionality of [Shopsys Framework](http://www.shopsys-framework.com/)
from its open-box repository [shopsys/project-base](https://github.com/shopsys/project-base)
    - this will allow the core to be upgraded via `composer update` in different project implementations
    - core functionality includes:
        - all Shopsys-specific Symfony commands
        - model and components with business logic and their data fixtures
        - Symfony controllers with form definitions, Twig templates and all javascripts of the web-based administration
        - custom form types, form extensions and twig extensions
        - compiler passes to allow basic extensibility with plugins (eg. product feeds)
    - this is going to be a base of a newly built architecture of [Shopsys Framework](http://www.shopsys-framework.com/)
- styles related to admin extracted from [shopsys/project-base](https://github.com/shopsys/project-base) package
    - this will allow styles to be upgraded via `composer update` in project implementations
- glass-box model entities are now extensible from project-base without changing the framework code
    - the entity extension is a work in progress
    - currently it would require you to override a lot of classes to use the extended entities instead of the parents
- [Shopsys Community License](https://github.com/shopsys/framework/blob/master/LICENSE)

#### Changed
- configuration of form types in administration is enabled using form type options
    -  following form types configured using options:
        - VatSettingsFormType
        - SliderItemFormType
        - ShopInfoSettingFormType
        - SeoSettingFormType
        - MailSettingFormType
        - LegalConditionsSettingFormType
        - HeurekaShopCertificationFormType
        - CustomerCommunicationFormType
        - CookiesSettingFormType
        - CategoryFormType
        - ArticleFormType
        - AdvertFormType
        - AdministratorFormType
        
### [shopsys/http-smoke-testing]
#### Changed
- added support of phpunit/phpunit ^6.0 and ^7.0 (@simara-svatopluk)

### [shopsys/product-feed-google]
#### Changed
- renamed [`TestStandardFeedItem`] to [`TestGoogleStandardFeedItem`]
- updated phpunit/phpunit to version 7

### [shopsys/product-feed-heureka]
#### Changed
- renamed [`TestStandardFeedItem`] to [`TestHeurekaStandardFeedItem`]
- updated phpunit/phpunit to version 7

### [shopsys/product-feed-zbozi]
#### Changed
- renamed [`TestStandardFeedItem`] to [`TestZboziStandardFeedItem`]
- updated phpunit/phpunit to version 7

### [shopsys/product-feed-interface]
#### Removed
- `HeurekaCategoryNameProviderInterface` as it is specific to Heureka product feed
   - [shopsys/product-feed-heureka](https://github.com/shopsys/product-feed-heureka) manages Heureka categories on its own since v0.5.0

### [shopsys/monorepo-tools]
#### Added
- bash scripts for building and splitting monolithic repository from existing packages
    - scripts are designed to be re-used even if different use-cases

### [shopsys/migrations]
#### Changed
- updated phpunit/phpunit to version 7
- DB migrations are installed from all registered bundles
    - they should be located in directory "Migrations" in the root of the bundle
- command `shopsys:migrations:generate` now offers a list of available bundles for generating the migrations

### [shopsys/plugin-interface]
#### Removed
- storing data via Data storage was removed
    - removed interfaces `PluginDataStorageProviderInterface` and `DataStorageInterface`
    - only Doctrine entities are further allowed for storing data

### [shopsys/shopsys]
#### Added
- basic infrastructure so that the monorepo can be installed and used as standard application
    - for details see [the Monorepo article](./docs/introduction/monorepo.md#infrastructure) in documentation
- [Shopsys Community License](./LICENSE)
- documentation was moved from [shopsys/project-base](https://github.com/shopsys/project-base)

### [shopsys/project-base]
#### Added
- Sessions are now stored in Redis
- Admin - Legal conditions: added personal data settings 
- Frontend site for requesting personal data information
    - Admin - added email template for personal data request
    - Frontend send email with link to personal data access site 
- [wip-glassbox-customization.md](docs/wip_glassbox/wip-glassbox-customization.md): new WIP documentation about working with glassbox
- docker: [`php-fpm/Dockerfile`](./project-base/docker/php-fpm/Dockerfile) added installation of `grunt-cli` to be able to run `grunt watch`
    - [`docker-compose.yml.dist`](docker/conf/docker-compose.yml.dist) and [`docker-compose-mac.yml.dist`](docker/conf/docker-compose-mac.yml.dist): opened port 35729 for livereload, that is used by `grunt watch`

#### Changed
- `JavascriptCompilerService` can now compile javascript from more than one source directory
    - the compiler supports subdirectory `common` in addition to `admin` and  `frontend`
- **the core functionality was extracted to a separate repository [shopsys/framework](https://github.com/shopsys/framework)**
    - this will allow the core to be upgraded via `composer update` in different project implementations
    - core functionality includes:
        - all Shopsys-specific Symfony commands
        - model and components with business logic and their data fixtures
        - database migrations
        - Symfony controllers with form definitions, Twig templates and all javascripts of the web-based administration
        - custom form types, form extensions and twig extensions
        - compiler passes to allow basic extensibility with plugins (eg. product feeds)
    - this is going to be a base of a newly built architecture of [Shopsys Framework](http://www.shopsys-framework.com/)
    - translations are extracted from both this repository and the framework package during `phing dump-translations`
        - this is because the translations are located solely in this package
- styles related to admin extracted into [shopsys/framework](https://github.com/shopsys/framework) package
    - this will allow styles to be upgraded via `composer update` in project implementations
- grunt now compiles less files also from [shopsys/framework](https://github.com/shopsys/framework) package
- updated phpunit/phpunit to version 7
- phing target dump-translations does not delete messages, that are not found in translated directories 
- docs updated in order to provide up-to-date information about the current project state 
- installation guides: updated instructions for creating new project from Shopsys Framework sources
- basics-about-package-architecture.md updated to reflect current architecture state
- updated doctrine/doctrine-fixtures-bundle
    - all fixtures now use autowiring
- services that are not obtained directly from container in the application are not defined as public anymore
    - IntegrationTestingBundle was removed
    - all services that must be public because of tests moved to services_test.yml
    - unnecessary service obtaining from container replaced with autowiring
- new images for no image and empty cart
- **the license was changed from MIT to [Shopsys Community License](./LICENSE)**

#### Removed
- documentation was moved into the main [Shopsys repository](https://github.com/shopsys/shopsys/blob/master/docs)

## Before monorepo
Before we managed to implement monorepo for our packages, we had slightly different versions for each of our package, we had stored our packages on internal server so we dont have PR available.
That's why is this section formatted differently.

### [shopsys/http-smoke-testing]
#### [1.1.0](https://github.com/shopsys/http-smoke-testing/compare/v1.0.0...v1.1.0) - 2017-11-01 
##### Added
- [CONTRIBUTING.md](https://github.com/shopsys/http-smoke-testing/blob/master/CONTRIBUTING.md)
 
##### Changed 
- Improved IDE auto-completion when customizing test cases via [`RouteConfig`](https://github.com/shopsys/http-smoke-testing/blob/master/src/RouteConfig.php)
    - Methods `changeDefaultRequestDataSet()` and `addExtraRequestDataSet()` now return new interface [`RequestDataSetInterface`](https://github.com/shopsys/http-smoke-testing/blob/master/src/RequestDataSetConfig.php). 
    - This new interface includes only a subset of methods in [`RequestDataSet`](https://github.com/shopsys/http-smoke-testing/blob/master/src/RequestDataSet.php) that is relevant to test case customization. 
 
#### [1.0.1](https://github.com/shopsys/http-smoke-testing/compare/v1.0.0...v1.0.1) - 2017-07-03 
##### Added 
- Unit test for RequestDataSetGenerator class
- This Changelog
 
#### 1.0.0 - 2017-05-23 
##### Added 
- Extracted HTTP smoke testing functionality from [Shopsys Framework](http://www.shopsys-framework.com/)
- `.travis.yml` file with Travis CI configuration

### [shopsys/product-feed-google]
#### [0.2.1](https://github.com/shopsys/product-feed-google/compare/v0.2.0...v0.2.1) - 2018-02-19
##### Fixed
- services.yml autodiscovery settings

#### [0.2.0](https://github.com/shopsys/product-feed-google/compare/v0.1.2...v0.2.0) - 2018-02-19
##### Changed
- services.yml updated to Symfony 3.4 best practices

#### [0.1.2](https://github.com/shopsys/product-feed-google/compare/v0.1.1...v0.1.2) - 2018-02-12
##### Fixed
- Fix availability value

#### [0.1.1](https://github.com/shopsys/product-feed-google/compare/v0.1.0...v0.1.1) - 2017-10-04
##### Added
- support for shopsys/plugin-interface 0.3.0
- support for shopsys/product-feed-interface 0.5.0

#### 0.1.0 - 2017-09-25
##### Added
- added basic logic of product feed for Google
- composer.json: added shopsys/coding-standards into require-dev

### [shopsys/product-feed-heureka]
#### [0.6.1](https://github.com/shopsys/product-feed-heureka/compare/v0.6.0...v0.6.1) - 2018-02-19
##### Changed
- updated package shopsys/form-types-bundle to version 0.2.0

#### [0.6.0](https://github.com/shopsys/product-feed-heureka/compare/v0.5.1...v0.6.0) - 2018-02-19
##### Changed
- services.yml updated to Symfony 3.4 best practices

#### [0.5.1](https://github.com/shopsys/product-feed-heureka/compare/v0.5.0...v0.5.1) - 2017-10-06
- names of Heureka categories are now cached by category ID in [`HeurekaFeedConfig`](./packages/product-feed-heureka/src/HeurekaFeedConfig.php)

#### [0.5.0](https://github.com/shopsys/product-feed-heureka/compare/v0.4.2...v0.5.0) - 2017-10-05
##### Added
- logic of Heureka categorization moved from [Shopsys Framework](https://www.shopsys-framework.com/) core repository 
    - Heureka categories are downloaded everyday via CRON module
    - extends CRUD of categories for assigning Heureka categories to categories on your online store
    - contains demo data fixtures

#### [0.4.2](https://github.com/shopsys/product-feed-heureka/compare/v0.4.1...v0.4.2) - 2017-10-05
##### Added
- support for shopsys/plugin-interface 0.3.0 
- support for shopsys/product-feed-interface 0.5.0 

#### [0.4.1](https://github.com/shopsys/product-feed-heureka/compare/v0.4.0...v0.4.1) - 2017-09-25
##### Added
- [CONTRIBUTING.md](https://github.com/shopsys/product-feed-heureka/blob/master/CONTRIBUTING.md)
##### Changed
- Dependency [product-feed-interface](https://github.com/shopsys/product-feed-interface) upgraded from ~0.3.0 to ~0.4.0
- [`HeurekaFeedConfig`](https://github.com/shopsys/product-feed-heureka/blob/master/src/HeurekaFeedConfig.php) now filters not sellable products 
- [`HeurekaFeedConfig`](https://github.com/shopsys/product-feed-heureka/blob/master/src/HeurekaFeedConfig.php) implemented method `getAdditionalData()` 
- [`TestStandardFeedItem`](https://github.com/shopsys/product-feed-heureka/blob/master/tests/TestStandardFeedItem.php) implemented method `getCurrencyCode()` 

#### [0.4.0](https://github.com/shopsys/product-feed-heureka/compare/v0.3.0...v0.4.0) - 2017-09-12
##### Added
- New dependencies for dev
    - phpunit/phpunit 5.7.21
    - twig/twig 1.34.0
    - twig/extensions 1.3.0
- New automatic test that is controlling right behaviour of plugin 
- Added travis build icon into [README.md](https://github.com/shopsys/product-feed-heureka/blob/master/README.md) 
##### Changed
- Dependency [shopsys/product-feed-interface] upgraded from ~0.2.0 to ~0.3.0 
##### Removed
- `HeurekaFeedConfig::getFeedItemRepository()` 

#### [0.3.0](https://github.com/shopsys/product-feed-heureka/compare/v0.2.0...v0.3.0) - 2017-08-09
##### Added
- This Changelog 
- UPGRADE.md 
- Plugin demo data (cpc for 2 domains) 
##### Changed
- Dependency [shopsys/plugin-interface] upgraded from ~0.1.0 to ~0.2.0 

#### [0.2.0](https://github.com/shopsys/product-feed-heureka/compare/v0.1.0...v0.2.0) - 2017-08-02
##### Added
- Retrieving custom plugin data 
    - Heureka category names
    - MAX_CPC (Maximum price per click)
- Extension of product form with custom field for MAX_CPC 
- New dependencies 
    - [shopsys/plugin-interface ~0.1.0](https://github.com/shopsys/plugin-interface)
    - [shopsys/form-types-bundle ~0.1.0](https://github.com/shopsys/form-types-bundle)
    - [symfony/form ^3.0](https://github.com/symfony/form)
    - [symfony/translation ^3.0](https://github.com/symfony/translation)
    - [symfony/validator ^3.0](https://github.com/symfony/validator)
##### Changed
- Dependency [shopsys/product-feed-interface] upgraded from ~0.1.0 to ~0.2.0 

### [shopsys/product-feed-zbozi]
#### [0.5.0](https://github.com/shopsys/product-feed-zbozi/compare/v0.4.2...v0.5.0) - 2018-02-19
##### Changed
- services.yml updated to Symfony 3.4 best practices 
- updated shopsys/form-types-bundle to version 0.2.0 

#### [0.4.2](https://github.com/shopsys/product-feed-zbozi/compare/v0.4.1...v0.4.2) - 2017-10-04
##### Added
- support for shopsys/plugin-interface 0.3.0 
- support for shopsys/product-feed-interface 0.5.0 

#### [0.4.1](https://github.com/shopsys/product-feed-zbozi/compare/v0.4.0...v0.4.1) - 2017-09-25
##### Added
- [CONTRIBUTING.md](https://github.com/shopsys/product-feed-zbozi/blob/master/CONTRIBUTING.md)
##### Changed
- Dependency [shopsys/product-feed-interface] upgraded from ~0.3.0 to ~0.4.0 
- [`ZboziFeedConfig`](https://github.com/shopsys/product-feed-zbozi/blob/master/src/ZboziFeedConfig.php) now filters not sellable products 
- [`ZboziFeedConfig`](https://github.com/shopsys/product-feed-zbozi/blob/master/src/ZboziFeedConfig.php) implemented method `getAdditionalData()` 
- [`TestStandardFeedItem`](https://github.com/shopsys/product-feed-zbozi/blob/master/tests/TestStandardFeedItem.php) implemented method `getCurrencyCode()` 

#### [0.4.0](https://github.com/shopsys/product-feed-zbozi/compare/v0.3.0...v0.4.0) - 2017-09-12
##### Added
- New dependencies for dev 
    - phpunit/phpunit >=5.0.0,<6.0
    - twig/twig 1.34.0
    - twig/extensions 1.3.0
- New automatic test that is controlling right behaviour of plugin 
- Added travis build icon into [README.md](https://github.com/shopsys/product-feed-zbozi/blob/master/README.md) 
##### Changed
- Dependency [product-feed-interface](https://github.com/shopsys/product-feed-zbozi/blob/master/shopsys/product-feed-interface) upgraded from ~0.2.0 to ~0.3.0 
##### Removed
- `ZboziFeedConfig::getFeedItemRepository()` 

#### [0.3.0](https://github.com/shopsys/product-feed-zbozi/compare/v0.2.0...v0.3.0) - 2017-09-06
##### Added
- This Changelog 
- UPGRADE.md 
- Plugin demo data (cpc, cpc_search and show for 2 domains) 
##### Changed
- Dependency [plugin-interface](https://github.com/shopsys/plugin-interface) upgraded from ~0.1.0 to ~0.2.0 

#### [0.2.0](https://github.com/shopsys/product-feed-zbozi/compare/v0.1.0...v0.2.0) - 2017-08-08
##### Added
- Retrieving custom plugin data and extension of product form with custom fields 
    - show (offer in feeds)
    - cpc (maximum price per click)
    - cpc_search (maximum price per click in offers)
- New dependencies 
    - [shopsys/plugin-interface ~0.1.0](https://github.com/shopsys/plugin-interface)
    - [shopsys/form-types-bundle ~0.1.0](https://github.com/shopsys/form-types-bundle)
    - [symfony/form ^3.0](https://github.com/symfony/form)
    - [symfony/translation ^3.0](https://github.com/symfony/translation)
    - [symfony/validator ^3.0](https://github.com/symfony/validator)
##### Changed
- Dependency [product-feed-interface](https://github.com/shopsys/product-feed-zbozi/blob/master/shopsys/product-feed-interface) upgraded from ~0.1.0 to ~0.2.0 

#### 0.1.0 - 2017-07-13
##### Added
- Extracted Zboží.cz product feed plugin from [Shopsys Framework](http://www.shopsys-framework.com/) 
- `.travis.yml` file with Travis CI configuration

### [shopsys/product-feed-heureka-delivery]
#### [0.3.0](https://github.com/shopsys/product-feed-heureka-delivery/compare/v0.2.2...v0.3.0) - 2018-02-19
##### Changed
- services.yml updated to Symfony 3.4 best practices
- updated phpunit/phpunit to version 7

#### [0.2.2](https://github.com/shopsys/product-feed-heureka-delivery/compare/v0.2.1...v0.2.2) - 2017-10-04
##### Added
- support for [shopsys/product-feed-interface] 0.5.0

#### [0.2.1](https://github.com/shopsys/product-feed-heureka-delivery/compare/v0.2.0...v0.2.1) - 2017-09-25
##### Added
- [CONTRIBUTING.md](https://github.com/shopsys/product-feed-heureka-delivery/blob/master/CONTRIBUTING.md)

##### Changed
- Dependency [shopsys/product-feed-interface] upgraded from ~0.3.0 to ~0.4.0

#### [0.2.0](https://github.com/shopsys/product-feed-heureka-delivery/compare/v0.1.1...v0.2.0) - 2017-09-12
##### Added
- This Changelog (@vitek-rostislav)
- New dependencies for dev(@MattCzerner)
    - phpunit/phpunit >=5.0.0,<6.0
    - twig/twig 1.34.0
    - twig/extensions 1.3.0
- New automatic test that is controlling right behaviour of plugin
- Added travis build icon into [README.md](https://github.com/shopsys/product-feed-heureka-delivery/blob/master/README.md)
##### Changed
- Dependency [plugin-interface](https://github.com/shopsys/product-feed-interface) upgraded from ~0.2.0 to ~0.3.0
##### Removed
- `HeurekaDeliveryFeedConfig::getFeedItemRepository()`

#### [0.1.1](https://github.com/shopsys/product-feed-heureka-delivery/compare/v0.1.0...v0.1.1) - 2017-08-18
##### Fixed
- Usage of `FeedItemInterface::getId()` method in `feed.xml.twig`
    - it was renamed from `FeedItemInterface::getItemId()` in [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface) v0.2.0

#### 0.1.0 - 2017-08-10
##### Added
- Extracted Heureka product delivery feed plugin from [Shopsys Framework](http://www.shopsys-framework.com/)
- `.travis.yml` file with Travis CI configuration

#### 0.1.0 - 2017-07-13
##### Added
- Extracted Heureka product feed plugin from [Shopsys Framework](http://www.shopsys-framework.com/)
- `.travis.yml` file with Travis CI configuration

### [shopsys/migrations]
#### [2.3.0](https://github.com/shopsys/migrations/compare/v2.2.0...v2.3.0 ) - 2018-02-19
##### Changed
- services.yml updated to Symfony 3.4 best practices

### [shopsys/product-feed-interface]
#### [0.5.0](https://github.com/shopsys/product-feed-interface/compare/v0.4.0...v0.5.0) - 2017-10-04
- [`StandardFeedItemInterface`](src/StandardFeedItemInterface.php) contains ID of its main category 

#### [0.4.0](https://github.com/shopsys/product-feed-interface/compare/v0.3.0...v0.4.0) - 2017-09-25
##### Added
- [CONTRIBUTING.md](CONTRIBUTING.md) 
- [template for github pull requests](docs/PULL_REQUEST_TEMPLATE.md) 
- [`StandardFeedItemInterface`](./packages/product-feed-interface/src/StandardFeedItemInterface.php) has new method `isSellingDenied()` 
- [`FeedConfigInterface`](./packages/product-feed-interface/src/FeedConfigInterface.php) has new method `getAdditionalInformation()` 
- [`StandardFeedItemInterface`](./packages/product-feed-interface/src/StandardFeedItemInterface.php) has new method `getCurrencyShortcut()` 

#### [0.3.0](https://github.com/shopsys/product-feed-interface/compare/v0.2.1...v0.3.0) - 2017-09-12
##### Added
- This Changelog
- UPGRADE.md
##### Removed
- `FeedItemRepositoryInterface`
- `FeedConfigInterface::getFeedItemRepository()`

#### [0.2.1](https://github.com/shopsys/product-feed-interface/compare/v0.2.0...v0.2.1) - 2017-08-17
##### Added
- New interface for delivery feed items - `DeliveryFeedItemInterface`

#### [0.2.0](https://github.com/shopsys/product-feed-interface/compare/v0.1.0...v0.2.0) - 2017-08-07
##### Changed
- `FeedItemInterface`: renamed method `getItemId()` to `getId()`
- `FeedItemCustomValuesProviderInterface` renamed to `HeurekaCategoryNameProviderInterface`
##### Removed
- General data storage functionality extracted into separate package [shopsys/plugin-interface](https://github.com/shopsys/plugin-interface)
    - removed `FeedItemCustomValuesProviderInterface::getCustomValuesForItems()` and `FeedItemCustomValuesInterface`

#### 0.1.0 - 2017-07-13
##### Added
- Package of interfaces providing compatibility between [Shopsys Framework](https://www.shopsys-framework.com) and product feed plugins.
- `.travis.yml` file with Travis CI configuration

### [shopsys/plugin-interface]
#### [0.3.0](https://github.com/shopsys/plugin-interface/compare/v0.2.0...v0.3.0) - 2017-10-04
##### Added
 - [CONTRIBUTING.md](https://github.com/shopsys/plugin-interface/blob/master/CONTRIBUTING.md)
 - `DataStorageInterface` can return all saved data via `getAll()`
 - `IteratedCronModuleInterface` and `SimpleCronModuleInterface`
 
#### [0.2.0](https://github.com/shopsys/plugin-interface/compare/v0.1.0...v0.2.0) - 2017-09-06
##### Added
 - This Changelog
 - interface for loading plugin's demo data
     - `PluginDataFixtureInterface`
 
#### 0.1.0 - 2017-08-04
##### Added
 - Package of interfaces providing compatibility between [Shopsys Framework](https://www.shopsys-framework.com) and plugins
     - interfaces responsible for retrieving and saving plugin custom data
         - `DataStorageInterface`
         - `PluginDataStorageProviderInterface`
     - interface responsible for extending CRUD with plugin custom sub-forms
         - `PluginCrudExtensionInterface`
 - `.travis.yml` file with Travis CI configuration

### [shopsys/project-base]
#### 6.0.0-beta21 - 2018-03-05
- released only in closed beta
##### Added
- PHPStan support (@mhujer)
    - currently analysing source code by level 0
- PHP 7.2 support 
- Uniformity of PHP and Postgres timezones is checked during the build
- in `TEST` environment `Domain` is created with all instances of `DomainConfig` having URL set to `%overwrite_domain_url%`
    - parameter is set only in `parameters_test.yml` as it is only relevant in `TEST` environment
    - overwriting can be switched off by setting the parameter to `~` (null in Yaml)
    - overwriting the domain URL is necessary for Selenium acceptance tests running in Docker
- LegalConditionsSetting: added privacy policy article selection
    - customers need to agree with privacy policy while registring, sending contact form and completing order process
- SubscriptionFormType: added required privacy policy agreement checkbox 
- subscription form: added link to privacy policy agreement article 
- NewsletterController now exports date of subscription to newsletter 
- `services_command.yml` to set Commands as services 
- [docker-troubleshooting.md](https://github.com/shopsys/shopsys/blob/master/docs/docker/docker-troubleshooting.md): added to help developers with common problems that occurs using docker for development
- Newsletter subscriber is distinguished by domain
    - Admin: E-mail newsletter now exports e-mails to csv for each domain separatedly
- DatabaseSearching: added getFullTextLikeSearchString() 
- admin: E-mail newsletter: now contains list of registered e-mails with ability to delete them

##### Changed
- cache is cleared before PHPUnit tests only when run via [Phing targets](https://github.com/shopsys/shopsys/blob/master/docs/introduction/phing-targets.md), not when run using `phpunit` directly 
- PHPUnit tests now fail on warning 
- end of support of PHP 7.0 
- renamed TermsAndCondition to LegalCondition to avoid multiple classes for legal conditions agreements 
- emails with empty subject or body are no longer sent
- postgresql-client is installed in [php-fpm/dockerfile](./project-base/docker/php-fpm/Dockerfile) for `pg_dump` function 
    - postgresql was downgraded to 9.5 because of compatibility with postgresql-client
- docker-compose: added container_name to smtp-server and adminer 
- configuration of Docker Compose tweaked for easier development 
    - `docker-compose.yml` is added to `.gitignore` for everyone to be able to make individual changes
    - the predefined templates are now in `/docker/conf` directory
    - `adminer` container uses port 1100 by default (as 1000 is often already in use)
    - Docker Sync is used only in configuration for MacOS as only there it is needed
    - `postgres` container is created with a volume for data persistence (in `var/postgres-data`)
    - see documentation of [Installation Using Docker](https://github.com/shopsys/shopsys/blob/master/docs/installation/installation-using-docker.md) for details
- default parameters in `parameters.yml.dist` and `parameters_test.yml.dist` are for Docker installation (instead of native) 
- Front/NewsletterController: extracted duplicit rendering and add return typehints 
- Symfony updated to version 3.4 
    - autowiring is now done via Symfony PSR-4
    - services now use FQN as naming convention
    - services are private by default
    - inlined services (called via container) are set to public
    - services required by another service are defined in services.yml (e.g. Shopsys\FrameworkBundle\Model\Administrator\Security\AdministratorUserProvider: ~)
    - all inline calls of services changed to use FQN
    - services no longer required in services.yml have been removed
    - services instanced after DI container creation are set as synthetic
- users and administrators are logged out of all the sessions except the current one on password change (this is required in Symfony 4) 
- running Phing without parameter now shows list of available targets instead of building application 
- updated presta/sitemap-bundle to version 1.5.2 in order to avoid deprecated calls 
 - updated SitemapListener to avoid using of deprecated SitemapListenerInterface
- updated symfony/swiftmailer-bundle to version 3.2.0 in order to fix deprecated calls 
- all calls of Form::isValid() are called only on submitted forms in order to prevent deprecated call 
- symlink so root/bin acts like root/project-base/bin  
- all commands are now services, that are lazy loaded with autowired dependencies  
- NewsletterFacadeTest: renamed properties to match class name 

##### Fixed
- `BrandFacade::create()` now generates friendly URL for all domains (@sspooky13)
- `Admin/HeurekaController::embedWidgetAction()` moved to new `Front/HeurekaController` as the action is called in FE template
- PHPUnit tests do not fail on Windows machine with PHP 7.0 because of excessively long file paths  
- customizeBundle.js: on-submit actions are no longer triggered when form validation error occurs 
- fixed google product feed availability values by updating it to v0.1.2 
- reloading of order preview now calls `Shopsys.register.registerNewContent()` (@petr.kadlec)  
- CurrentPromoCodeFacace: promo code is not searched in database if code is empty (@petr.kadlec)
- CategoryRepository::getCategoriesWithVisibleChildren() checks visibility of children (@petr.kadlec)
- added missing migration for privacy policy article 
- OrderStatusFilter: show names in labels instead of ids 
- legal conditions text in order 3rd step is not HTML escaped anymore  
- product search now does not cause 500 error when the search string ends with backslash

##### Removed
- PHPStorm Inspect is no longer used for static analysis of source code 
- Phing targets standards-ci and standards-ci-diff because they were redundant to standards and standards-diff targets 
- deprecated packages `symplify/controller-autowire` and `symplify/default-autowire` 

#### 6.0.0-beta20 - 2017-12-11
- released only in closed beta

##### Changed
- Docker `nginx.conf` has been upgraded with better performance settings 
    - JavaScript and CSS files are compressed with GZip
    - static content has cache headers set in order to leverage browser cache
##### Fixed
- miscellaneous annotations, typos and other minor fixes (@petr.kadlec)
- `CartController::addProductAction()`: now uses `Request` instance passed as the method argument (Symfony 3 style) instead of calling the base `Controller` method `getRequest()` (Symfony 2.x style) (@petr.kadlec)
    - see [Symfony upgrade log](https://github.com/symfony/symfony/blob/3.0/UPGRADE-3.0.md#frameworkbundle) for more information
- `ExecutionContextInterface::buildViolation()` (Symfony 3 style) is now used instead of `ExecutionContextInterface::addViolationAt()` (Symfony 2.x style) (@petr.kadlec)
    - see [Symfony upgrade log](https://github.com/symfony/symfony/blob/3.0/UPGRADE-3.0.md#validator) for more information

#### 6.0.0-beta19.2 - 2017-11-23
- released only in closed beta

##### Fixed
- updated symfony/symfony to v3.2.14 in order to avoid known security vulnerabilities 

#### 6.0.0-beta19.1 - 2017-11-21
- released only in closed beta

##### Fixed
- coding standards check "phing standards" passes

#### 6.0.0-beta19 - 2017-11-21
- released only in closed beta

##### Added
- size of performance data fixtures and limits for performance testing are now configurable via parameters defined in [`parameters_common.yml`](./project-base/app/config/parameters_common.yml) 
- performance tests report database query counts 
- UserDataFixture: alias for SettingValueDataFixture to fix [PHP bug #66862](https://bugs.php.net/bug.php?id=66862)

##### Changed
- parameters that are in `parameters.yml` or `parameters_test.yml` that are not in their `.dist` templates are not removed during `composer install` anymore 
- customer creating controllers are not catching exception for duplicate email, it is not necessary since it is done by UniqueEmail constraint now 
- input "remember me" in login form is encapsulated by its label for better UX

#### 6.0.0-beta18 - 2017-10-19
- released only in closed beta

##### Added
- [coding standards documentation](https://github.com/shopsys/shopsys/blob/master/docs/contributing/coding-standards.md)
- acceptance tests asserting successful image upload in admin for product, transport and payment
- Docker based server stack for easier installation and development 
    - see [Installation Using Docker](https://github.com/shopsys/shopsys/blob/master/docs/installation/installation-using-docker.md) for details
- plugins can now extend the CRUD of categories (using `CategoryFormType`) 

##### Changed
- cache deletion before running unit tests is now done using `Symfony\Filesystem` instead of using console command 
    - deleting via console command `cache:clear` is slow, because it creates whole application container first and then deletes all cache created in process
- Windows locales list: use more tolerant name for Czech locale
    - in Windows 2017 Fall Creators Update the locale name was changed from "Czech_Czech Republic" to "Czech_Czechia"
    - name "Czech" is acceptable in all Windows versions
- interfaces for CRON modules moved to [shopsys/plugin-interface](https://github.com/shopsys/plugin-interface) 
- `ImageDemoCommand` now prompts to truncate "images" db table when it is not empty before new demo images are loaded

##### Deleted
- logic of Heureka categorization moved to [shopsys/product-feed-heureka](https://github.com/shopsys/product-feed-heureka) 
    - all your current Heureka category data will be migrated into the new structure

##### Fixed
- proper `baseUrl` value from `domains_urls.yaml` is now stored into `settings` when creating new domain

#### 6.0.0-beta17 - 2017-10-03
- released only in closed beta

##### Added
- MIT license 
- phing targets `eslint-check`, `eslint-check-diff`, `eslint-fix` and `eslint-fix-diff` to check and fix coding standards in JS files (@sspooky13)
    - executed as a part of targets `standards`, `standards-diff`, `standards-fix` and `standards-fix-diff`
- [product feed plugin for Google](https://github.com/shopsys/product-feed-google/) 
- new article explaining [Basics About Package Architecture](https://github.com/shopsys/shopsys/blob/master/docs/introduction/basics-about-package-architecture.md)

##### Changed
- `StandardFeedItemRepository`: now selects available products instead of sellable, filtering of not sellable products is made in product plugins 
- implementations of `StandardFeedItemInterface` now must have implemented methods `isSellingDenied()` and `getCurrencyCode()`
- implementations of `FeedConfigInterface` now must have implemented method `getAdditionalInformation()` 

#### 6.0.0-beta16 - 2017-09-19
- released only in closed beta

##### Added
- new command `shopsys:plugin-data-fixtures:load` for loading demo data from plugins 
    - called during build of demo database
- new documentation about Shopsys Framework model architecture 
- `FeedItemRepositoryInterface`
    - moved from [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/)
- [template for github pull requests](https://github.com/shopsys/shopsys/blob/master/docs/PULL_REQUEST_TEMPLATE.md)

##### Changed
- dependency [shopsys/plugin-interface](https://github.com/shopsys/plugin-interface/) upgraded from 0.1.0 to 0.2.0 
- dependency [shopsys/product-feed-heureka](https://github.com/shopsys/product-feed-heureka/) upgraded from 0.2.0 to 0.4.0 
- dependency [shopsys/product-feed-zbozi](https://github.com/shopsys/product-feed-zbozi/) upgraded from 0.2.0 to 0.4.0 
- dependency [shopsys/product-feed-heureka-delivery](https://github.com/shopsys/product-feed-heureka-delivery/) upgraded from 0.1.1 to 0.2.0
- dependency [shopsys/product-feed-interface](https://github.com/shopsys/product-feed-interface/) upgraded from 0.2.1 to 0.3.0
- it is no longer needed to redeclare feed plugin's implementations of `FeedConfigInterface` in `services.yml`
    - decision about providing proper instance of `FeedItemRepositoryInterface` is made in `FeedConfigFacade`
- FeedConfigRepository renamed to `FeedConfigRegistry` 
    - it is not fetching data from Doctrine as other repositories, it only serves as a container for registering services of specific type
    - similar to `PluginDataFixtureRegistry` or `PluginCrudExtensionRegistry`
- `UknownPluginDataFixtureException` renamed to `UnknownPluginCrudExtensionTypeException` because of a typo 
- `FeedConfigRegistry` now contains all FeedConfigs in one array (indexed by type)
    - definition and assertion of known feed configs types moved from [`RegisterProductFeedConfigsCompilerPass`](./src/Shopsys/ShopBundle/DependencyInjection/Compiler/RegisterProductFeedConfigsCompilerPass.php) to `FeedConfigRegistry`
    - changed message and arguments of `UnknownFeedConfigTypeException`
- renamed methods working with standard feeds only to be more expressive 
    - renamed `FeedConfigFacade::getFeedConfigs()` to `getStandardFeedConfigs()`
    - renamed `FeedFacade::generateFeedsIteratively()` to `generateStandardFeedsIteratively()`
    - renamed `FeedGenerationConfigFactory::createAll()` to `createAllForStandardFeeds()`
- [`parameters.yml.dist`](./project-base/app/config/parameters.yml.dist): renamed parameter `email_for_error_reporting` to `error_reporting_email_to`
- sender email for error reporting is now configured in [`parameters.yml.dist`](./project-base/app/config/parameters.yml.dist)
- reimplemented `CategoriesType` (@Petr Heinz)
    - it now extends `CollectionType` instead of `ChoiceType`
    - it loads only those categories that are needed to show all selected categories in a tree, not all of them
    - collapsed categories can be loaded via AJAX
- `CategoryRepository::findById()` now uses `find()` method of Doctrine repository instead of query builder so it can use cached results 
- it is possible to mention occurrences of an image size in [`images.yml`](./project-base/src/Shopsys/ShopBundle/Resources/config/images.yml) 
    - previously they were directly in `ImageController`
    - they are not translatable anymore (too hard to maintain)

##### Removed
- email for error reporting removed from [`parameters_test.yml.dist`](./project-base/app/config/parameters_test.yml.dist)
- removed unused private properties from classes 
- removed `CategoriesTypeTransformerFactory` 
    - the `CategoriesTypeTransformer` can be fully autowired after deletion of `$domainId`

##### Fixed
- [`InlineEditPage::createNewRow()`](./project-base/tests/ShopBundle/Acceptance/acceptance/PageObject/Admin/InlineEditPage.php) now waits for AJAX to complete 
    - fixes false negatives of acceptance test [`PromoCodeInlineEditCest::testPromoCodeCreate()`](./project-base/tests/ShopBundle/Acceptance/acceptance/PromoCodeInlineEditCest.php)

#### 6.0.0-beta15 - 2017-08-31
- previous beta versions released only internally (mentioned changes since 6.0.0-alpha)
- this version was released only in closed beta

##### Added
- PHP 7 support
- [a basic knowledgebase](https://github.com/shopsys/shopsys/blob/master/docs/index.md)
    - installation guide
    - guidelines for contributions
    - cookbooks
    - articles on automated testing

##### Changed
- update to Symfony 3
- PSR-2 compliance
- English as a main language
    - language of first front-end domain
    - language of administration
    - all translatable message sources in English

##### Deleted
- separation of HTTP smoke test module into a component:
    - https://github.com/shopsys/http-smoke-testing/
- separation of product feed modules into plugins:
    - https://github.com/shopsys/plugin-interface/
    - https://github.com/shopsys/product-feed-interface/
    - https://github.com/shopsys/product-feed-heureka/
    - https://github.com/shopsys/product-feed-heureka-delivery/
    - https://github.com/shopsys/product-feed-zbozi/

#### 6.0.0-alpha - 2016-11-09
- developed since 2014-03-31
- used only as internal platform for e-commerce projects of Shopsys Agency
- released only internally

##### Added
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

### [shopsys/coding-standards]
#### [4.0](https://github.com/shopsys/coding-standards/compare/v3.1.1...v4.0.0) - 2018-01-27
##### Added
- composer script `run-all-checks` for easier testing of the package (@TomasVotruba)

##### Changed
- `OrmJoinColumnRequireNullableFixer` marked as *risky* (@sustmi)
- [#11](https://github.com/shopsys/coding-standards/pull/11) dropped support of PHP 7.0 
- [#12](https://github.com/shopsys/coding-standards/pull/12/) [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard) is now used (@TomasVotruba)
    - the tool encapsulates PHP-CS-Fixer and PHP_CodeSniffer 
    - rules configuration is now unified in single file - [`easy-coding-standard.neon`](./packages/coding-standards/easy-coding-standard.neon)
    - the option `ignore-whitespace` for rules checking method and class length is not available anymore
        - the limits were increased to 550 (class length) and 60 (method length)

##### Removed
- PHP Mess Detector (@TomasVotruba)
- line length sniff (@TomasVotruba)

#### [3.1.1](https://github.com/shopsys/coding-standards/compare/v3.1.0...v3.1.1) - 2017-10-31
##### Fixed
- enabled custom fixers

#### [3.1.0](https://github.com/shopsys/coding-standards/compare/v3.0.2...v3.1.0) - 2017-10-12
##### Added
- This changelog 
- [Description of used coding standards rules](./packages/coding-standards/docs/description-of-used-coding-standards-rules.md) 
- New rules in [phpcs-fixer ruleset](./packages/coding-standards/build/phpcs-fixer.php_cs):
    - combine_consecutive_unsets
    - function_typehint_space
    - hash_to_slash_comment
    - lowercase_cast
    - native_function_casing
    - no_empty_comment
    - no_empty_phpdoc
    - no_spaces_around_offset
    - no_unneeded_control_parentheses
    - no_useless_return
    - no_whitespace_before_comma_in_array
    - non_printable_character
    - normalize_index_brace
    - phpdoc_annotation_without_dot
    - phpdoc_no_useless_inheritdoc
    - phpdoc_single_line_var_spacing
    - protected_to_private
    - semicolon_after_instruction
    - short_scalar_cast
    - space_after_semicolon
    - whitespace_after_comma_in_array

##### Changed
- friendsofphp/php-cs-fixer upgraded from version 2.1 to version 2.3 
- [phpcs-fixer ruleset](./build/phpcs-fixer.php_cs) 
    - replaced deprecated "hash_to_slash_comment" rule with "single_line_comment_style" rule
    - custom NoUnusedImportsFixer replaced with standard "no_unused_imports" rule

##### Deleted
- Redundant rules which were already covered by other rules

### [shopsys/form-types-bundle]
#### [0.2.0](https://github.com/shopsys/form-types-bundle/compare/v0.1.0...v0.2.0) - 2018-02-19
##### Added
- This Changelog
- [CONTRIBUTING.md](https://github.com/shopsys/form-types-bundle/blob/master/CONTRIBUTING.md)
##### Changed
- services.yml updated to Symfony 3.4 best practices

#### 0.1.0 - 2017-08-04
##### Added
- Custom form types extracted from [Shopsys Framework](http://www.shopsys-framework.com/), see [README](https://github.com/shopsys/form-types-bundle/blob/master/README.md) for more information
    - MultidomainType
    - YesNoType
- `.travis.yml` file with Travis CI configuration

[Unreleased]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha4...HEAD
[7.0.0-alpha4]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha3...v7.0.0-alpha4
[7.0.0-alpha3]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha2...v7.0.0-alpha3
[7.0.0-alpha2]: https://github.com/shopsys/shopsys/compare/v7.0.0-alpha1...v7.0.0-alpha2

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

[@pk16011990]: https://github.com/pk16011990
[@stanoMilan]: https://github.com/stanoMilan
[@EdoBarnas]: https://github.com/EdoBarnas
[@DavidKuna]: https://github.com/DavidKuna
[@lukaso]: https://github.com/lukaso
[@TomasVotruba]: https://github.com/TomasVotruba
