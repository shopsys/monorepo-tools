# [Upgrade from v7.0.0-beta5 to Unreleased]

This guide contains instructions to upgrade from version v7.0.0-beta5 to Unreleased.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
### Infrastructure
- *(optional)* in your `docker/php-fpm/Dockerfile` change base image to `php:7.3-fpm-stretch` ([#694](https://github.com/shopsys/shopsys/pull/694))
- add subnet of your Kubernetes cluster as ENV variable into the config `/project-base/kubernetes/deployments/smtp-server.yml` for the pod of smtp container ([#777](https://github.com/shopsys/shopsys/pull/777))  
for instance:
    ```yaml
    image: namshi/smtp:latest
    env:
    -   name: RELAY_NETWORKS
        value: :192.168.0.0/16:10.0.0.0/8:172.16.0.0/12
    ```

### Tools
- *(optional)* add a new phing target `clean-redis` to your `build.xml` and `build-dev.xml` and use it where you need to clean Redis cache.
  You can find an inspiration in [#736](https://github.com/shopsys/shopsys/pull/736/files)
    ```xml
        <target name="clean-redis" description="Cleans up redis cache">
            <exec executable="${path.php.executable}" passthru="true" checkreturn="true" output="${dev.null}">
                <arg value="${path.bin-console}" />
                <arg value="shopsys:redis:clean-cache" />
            </exec>
        </target>
    ```
- *(optional)* add a new [script](https://github.com/shopsys/shopsys/pull/759/files#diff-e5f46a7c45e95214037078344ce17721) to `scripts/install.sh`
    - this script serves as a fast way to install demo instance of Shopsys Framework.
    - also this script can be used if you change the configuration of docker or app, script will apply all the changes done in these files and rebuild images.

### Database migrations
- after running database migrations, all your countries across domains will be merged together and original names will be added as translations
    - all your countries have to have country code filled in
    - country not present on some domain will use country code as its name and will be disabled on that domain
    - [`Version20190121094400`](https://github.com/shopsys/shopsys/blob/master/packages/framework/src/Migrations/Version20190121094400.php <!--- TODO: change to released version instead of master -->

### Application
- use configuration file to define directories that needs to be created during build of the application ([#781](https://github.com/shopsys/shopsys/pull/781))
    - create new configuration file `app/config/directories.yml` with 2 types of directories `public_directories` and `internal_directories` and add this file into `$configs` array in `AppKernel::getConfigs()`.
    - if you had implemented your individual directories in `CreateApplicationDirectoriesCommand`, delete your extension of a class and add the directories into `app/config/directories.yml` and fill them into sections, you can read more about sections [here](https://github.com/shopsys/shopsys/blob/master/docs/intruduction/directories.yml) <!--- TODO: change to released version instead of master -->
- if you were using `oneup/flysystembundle` for using different adapter than the local one, you must now implement `FilesystemFactoryInterface` and init the adapter by yourself.
- *(optional)* delete dependency on `oneup/flysystembundle` from your `composer.json`
- remove usages of inherited `OrderItem` classes ([#715](https://github.com/shopsys/shopsys/pull/715))
    - replace usages of `OrderProduct`, `OrderPayment`, and `OrderTransport` with common `OrderItem`
        - use `isType<type>()` method instead of `instanceof`
    - replace usages of `OrderTransportData`, `OrderPaymentData` with common `OrderItemData`
    - replace usages of `OrderProductFactoryInterface`, `OrderPaymentFactoryInterface` and `OrderTransportFactoryInterface` with common `OrderItemFactoryInterface`
        - replace usages of `OrderProductFactory`, `OrderPaymentFactory` and `OrderTransportFactory` with `OrderItemFactory`
        - replace usages of method `create()` with `createProduct()`, `createPayment()` or `createTransport()`, respectively
    - replace usages of `OrderPaymentDataFactoryInterface` and `OrderTransportDataFactoryInterface` with common `OrderItemDataFactoryInterface`
        - replace usages of `OrderPaymentDataFactory` and `OrderTransportDataFactory` with common `OrderItemDataFactory`
        - replaces usages of method `createFromOrderPayment()` and `createFromOrderTransport()` with `createFromOrderItem()`
    - following classes changed constructors, if you extend them, change them appropriately:
        - `Order`
        - `OrderDataFactory`
        - `OrderItemFacade`
        - `OrderFacade`
    - remove non-existing test cases from `EntityExtensionTest`
        - remove `ExtendedOrder*` classes
        - remove calling `doTestExtendedEntityInstantiation` with classes that are removed
        - change `ExtendedOrderItem` to standard class - remove `abstract` and inheritance annotations
        - change `doTestExtendedOrderItemsPersistence` to test only `OrderItem`
        - please find inspiration in [#715](https://github.com/shopsys/shopsys/pull/715/files)
- *(optional)* to allow [support for multiple image sizes #766](https://github.com/shopsys/shopsys/pull/766) you have to
    - implement action `getAdditionalImageAction()` in `Front/ImageController.php` (or copy it from [ImageController.php](https://github.com/shopsys/project-base/blob/master/src/Shopsys/ShopBundle/Controller/Front/ImageController.php))
    - add routes into your frontend router
      ```yml
        front_additional_image:
            path: /%shopsys.content_dir_name%/images/{entityName}/{type}/{sizeName}/additional_{additionalIndex}_{imageId}.{extension}
            defaults: { _controller: ShopsysShopBundle:Front\Image:getAdditionalImage }
            requirements:
                imageId: \d+
                additionalIndex: \d+

        front_additional_image_without_type:
            path: /%shopsys.content_dir_name%/images/{entityName}/{sizeName}/additional_{additionalIndex}_{imageId}.{extension}
            defaults:
                _controller: ShopsysShopBundle:Front\Image:getAdditionalImage
                type: ~
            requirements:
                imageId: \d+
                additionalIndex: \d+
      ```
- if you extended `EntityExtensionParentMetadataCleanerEventSubscriber`, review whether you need to implement [fix for translation entities #748](https://github.com/shopsys/shopsys/pull/748) or whether you still need the extension at all

`Cart` has been slightly refactored ((#765)[https://github.com/shopsys/shopsys/pull/765/]), so change your usages appropriately:
 - property `cartItems` has been renamed to `items`
 - method `getCartItemById` has been renamed to `getItemById`
 - method `getQuantifiedProductsIndexedByCartItemId` has been renamed to `getQuantifiedProductsIndexedByItemId`
 - method `findSimilarCartItemByCartItem` has been renamed to `findSimilarItemByItem`

`Cart` is now entity, so change your usages appropriately:
 - properties `user` and `cart_identifier` were moved from `CartItem` to `Cart`
    - there are new methods `getCartIdentifier`, `setModifeiedNow` and `setModifiedAt`
 - added new property `modifiedAt`
 - method's `addProduct` first parameter `CustomerIdentifier` has been removed
 - method's `mergeWithCart` third parameter `CustomerIdentifier` has been removed
 - the implementation of methods `addItem`, `removeItemyId`, `getItems`, `getItemsCount`, `changeQuantities`, `getQuantifiedProductsIndexedByItemId`, `mergeWithCart` and `findSimilarItemByItem` has been changed so revise them if you extended them

`CartFactory` has been refactored, so change your usages appropriately:
 - methods `get` and `createNewCart` has been removed, use `CartFacade`'s `findCartOfCurrentCustomer`, `findCartByCustomerIdentifier`, `getCartOfCurrentCustomerCreateIfNotExists` or `getCartByCustomerIdentifierCreateIfNotExists` instead
    - methods starting with `find` can return `null`
    - methods ending with `CreateIfNotExists` will always create new `Cart` in database, so use this methods only in case you are adding some items
 - method `clearCache` has been removed
 - attributes `carts`, `cartItemRepository` and `cartWatcherFacade` has been removed

`CartFacade` has been refactored, so change your usages appropriately:
 - it has four new methods for working with `Cart`
    - methods `findCartOfCurrentCustomer` and `findCartByCustomerIdentifier` will return `Cart` if it contains at least one `CartItem` or `null` if it is empty
    - methods `getCartOfCurrentCustomerCreateIfNotExists` and `getCartByCustomerIdentifierIfNotExists` will return `Cart` if it contains at least one `CartItem` or create one if it does not
 - the implementation of methods `addProductToCart`, `changeQuantities`, `deleteCartItem`, `getQuantifiedProductsOfCurrentCustomerIndexedByCartItemId` and `deleteOldCarts` has been changed so revise them if you extended them
 - method `cleanCart` has been removed, use method `deleteCart` instead

`CartMigrationFacade` has been refactored, so change your usages appropriately:
 - the constructor has changed
 - implementation of methods `mergeCurrentCartWithCart` and `onKernelController` has been changed, so revise them if you extended them

 `CartItemRepository` class has been removed and all its logic has been moved to new `CartRepository`
 `CartItemFactory::create` and `CartItemFactoryInterface::create` methods has changed, its first parameter is no longer `CustomerIdentifier` but `Cart`

 In twig templates (e.g. `Front/Content/Cart/index.html.twig` and `Front/Inline/Cart/cartBox.html.twig`), use `cart is not null` instead of `cart.itemsCount > 0` and `cart.isEmpty`

 You would probably need to modify your tests as well, because of changes in `Cart`. You would need to revise the following classes:
 - `Unit/Model/Cart/CartFactoryTest.php` has been removed
 - `Unit/Model/Cart/CartTest.php` has been changed
 - `Functional/Model/Cart/CartFacadeDeleteOldCartsTest.php` has been changed
 - `Functional/Model/Cart/CartFacadeTest.php` has been changed
 - `Functional/Model/Cart/CartItemTest.php` has been changed
 - `Functional/Model/Cart/CartTest.php` has been changed
 - `Functional/Model/Cart/Watcher/CartWatcherTest.php` has been changed
 - `Functional/Model/Order/OrderFacadeTest.php` has been changed
- *(optional)* upgrade npm packages to the latest version ([#755](https://github.com/shopsys/shopsys/pull/755))
    - remove all npm packages by removing folder `project-base/node_modules` and `project-base/package-lock.json`
    - run command `php phing npm`
    - in order to pass standards tests you also need to run `php phing eslint-fix` to let ESlint npm package update your JavaScript files. After that your syntax should be updated to latest JavaScript standards checked by ESLint.
- unify countries across domains with translations and domain dependency ([#762](https://github.com/shopsys/shopsys/pull/762))
    - fix new entity `Country` creation (either using factory or directly) as it changed its constructor and `CountryFactory::create` method signature (removed argument `domainId`)
        - do not forget to fix `PersonalDataExportXmlTest`
    - adjust usages of `CountryFacade`
        - method `create` no longer has second argument `domainId`
        - remove usages of methods `getAllByDomainId` and `getAllOnCurrentDomain` as they were deleted
            - use new methods `getAllEnabledOnDomain` and `getAllEnabledOnCurrentDomain` (methods returns only enabled countries)
            - change usages in `BillingAddressFormType`, `DeliveryAddressFormType` and `PersonalInfoFormType` in your implementation
            - fix `CountryFacade` mock in `PersonalInfoFormTypeTest` – mock method `getAllEnabledOnDomain` instead of `getAllByDomainId`
    - fix usages of method `Country::getName` as it now needs proper locale as an argument
    - change usages of property `name` in `CountryData` to array because it is now localized
    - remove usages of method `CountryRepository::getAllByDomainId` – use `CountryRepository::getAllEnabledByDomainIdWithLocale` instead
    - if you have extended `CountryDataFactory` revise your changes as countries are now localized and domain dependent
    - adjust data fixtures, if you have your own
        - remove `MultiDomainCountryDataFixture` as it does not make sense now and change dependency from `MultiDomainCountryDataFixture` to `CountryDataFixture` (in `MultiDomainOrderDataFixture`, `MultiDomainUserDataFixture`, `OrderDataFixture` and `UserDataFixture`)
        - in `MultiDomainOrderDataFixture`, `MultiDomainUserDataFixture`, `OrderDataFixture`, `UserDataFixture` change obtaining reference to country from `getReferenceForDomain` to `getReference` (without domain)
    - class `CountryInlineEdit` (inline editable country grid) is no longer available, remove usages in favor of `CountryGridFactory`
    - if you have extended `CountryGridFactory`, revise your changes because class changed its namespace
    - if you have extended `CountryFormType`, revise your changes – new fields are available
    - if you have extended `CountryController` revise your changes – `new` and `edit` actions were added
- if you have extended `Localization` class, you have to add type-hints to extended methods because they were added in the parent class ([#806](https://github.com/shopsys/shopsys/pull/806))
    - if you have extended method `Localization::getAdminLocale()` only to have administration in a different language than english, you can delete it and set parameter `shopsys.admin_locale` in your `parameters.yml` file instead

- if you have extended classes from `Shopsys\FrameworkBundle\Model`, `Shopsys\FrameworkBundle\Component` or `Shopsys\FrameworkBundle\DataFixtures\Demo` namespace ([#788](https://github.com/shopsys/shopsys/pull/788))
    - you need to adjust extended methods and fields to `protected` visibility because all `private` visibilities from these namespaces were changed to `protected`
    - you can delete methods that you just copied due to inability to inherit
- Microservices has been removed and their funcionality has been moved to framework ([#793](https://github.com/shopsys/shopsys/pull/793/)):
    - classes from microservices has been moved to `Shopsys\FrameworkBundle\Model\Product\Search` namespace
    - definitions of indexes has been moved to project folder `src/Shopsys/ShopBundle/Resources/definition`, copy them to your project as well
        - add `shopsys.elasticsearch.structure_dir: '%shopsys.resources_dir%/definition/'` to your `paths.yml` config file
    - Symfony commands for Elasticsearch management has changed their namespace from `shopsys:microservice:product-search` to `shopsys:product-search`
        - update your `build.xml` and `build-dev.xml` to reflect this change
        - if you have extended those command in your project, update their class and file names and `$defaultName` property appropriately
    - `StructureManager` has been moved to `packages/framework/src/Component/Elasticsearch` and renamed to `ElasticsearchStructureManager` update your code if you extended or used it
        - its methods `getIndexName`, `createIndex`, `deleteIndex` and `getDefinition` now accept second mandatory parameter `index`  update your code if you are using them
            - this parameter is for distinguishing different use cases like Product search
    - this classes has been moved, update your code if you extended them or used them in your code:
        - `ProductSearchExportFacade` has been moved from `Shopsys\FrameworkBundle\Model\Product\ProductSearchExport` to `Shopsys\FrameworkBundle\Model\Product\Search\Export`
        - `ProductSearchExportRepository` has been moved from `Shopsys\FrameworkBundle\Model\Product\ProductSearchExport` to `Shopsys\FrameworkBundle\Model\Product\Search\Export`
        - `ProductSearchExportCronModule` has been moved from `Shopsys\FrameworkBundle\Model\Product\ProductSearchExport` to `Shopsys\FrameworkBundle\Model\Product\Search\Export`
    - Phing target `microservices-check` has been removed because it is no longer necessary
        - update your `build-dev.xml` to reflect this change
    - Phing targets starting with `microservice` has this prefix removed e.g. `microservice-product-search-create-structure` has been renamed to `product-search-create-structure`
        - update your `build.xml`,`build-dev.xml` and `kubernetes/deployments/webserver-php-fpm.yml` to reflect this change
    - run `composer install` so you will be prompted to set value for new parameter `elasticsearch_host`
    - remove all microservice Docker services and volumes from your `docker-compose` and `docker-sync` files
    - update your `php-fpm/Dockerfile` to include default ENV variable `ELASTIC_SEARCH_INDEX_PREFIX` or set this environment while building `php-fpm` image
    - if you are using Kubernetes with our prepared scripts remove all rows including word microservice from all scripts in `.ci` folder
        - only exception is `restart_kubernetes.sh` and line starting `kubectl exec` where you remove `microservices-` prefix in names of Phing targets
    - remove microservices from `kubernetes` `deployments` and `services` folders and update `kubernetes/kustomize/base/kustomization.yaml` appropriately
    - `ProductSearchExportRepositoryTest` move from `Tests\ShopBundle\Functional\Model\Product\ProductSearchExport` to `Tests\ShopBundle\Functional\Model\Product\Search`
    - in production you will need to run `product-search-recreate-structure` Phing target while next build to create indexes again with new name
        - after that remove previous indexes used for Product search, so they do not consume any memory ([link to Elasticsearch documentation](https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-delete-index.html))
- warm up the production cache before generating error pages ([#816](https://github.com/shopsys/shopsys/pull/816))
    - in `build.xml`, create a new phing target `prod-warmup`:
    ```xml
    <target name="prod-warmup" description="Warms up cache for production environment.">
        <exec executable="${path.php.executable}" passthru="true" checkreturn="true">
            <arg value="${path.bin-console}" />
            <arg value="cache:warmup" />
            <arg value="--env=prod" />
        </exec>
    </target>
    ```
    - add a dependency on this target to `error-pages-generate`:
    ```diff
    - <target name="error-pages-generate" description="...">
    + <target name="error-pages-generate" depends="prod-warmup" description="...">
    ```

## [shopsys/product-feed-heureka]
- if you have extended class HeurekaCategoryDownloader or HeurekaCategoryCronModule ([#788](https://github.com/shopsys/shopsys/pull/788))
    - you need to adjust already extended methods and fields to `protected` visibility because all `private` visibilities from these namespaces were changed to `protected`
    - you can delete methods that you just copied due to inability to inherit

[Upgrade from v7.0.0-beta5 to Unreleased]: https://github.com/shopsys/shopsys/compare/v7.0.0-beta5...HEAD
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
