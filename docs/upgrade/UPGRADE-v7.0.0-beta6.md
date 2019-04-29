# [Upgrade from v7.0.0-beta5 to v7.0.0-beta6]

This guide contains instructions to upgrade from version v7.0.0-beta5 to v7.0.0-beta6.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

*Note: instructions marked as "low priority" are not vital, however, we recommend to perform them as well during upgrading as it might ease your work in the future.*

## [shopsys/framework]
### Infrastructure
- *(low priority)* in your `docker/php-fpm/Dockerfile` change base image to `php:7.3-fpm-stretch` ([#694](https://github.com/shopsys/shopsys/pull/694))
- add subnet of your Kubernetes cluster as ENV variable into the config `/project-base/kubernetes/deployments/smtp-server.yml` for the pod of smtp container ([#777](https://github.com/shopsys/shopsys/pull/777))  
for instance:
    ```yaml
    image: namshi/smtp:latest
    env:
    -   name: RELAY_NETWORKS
        value: :192.168.0.0/16:10.0.0.0/8:172.16.0.0/12
    ```

### Tools
- *(low priority)* add a new phing target `clean-redis` to your `build.xml` and `build-dev.xml` and use it where you need to clean Redis cache.
  You can find an inspiration in [#736](https://github.com/shopsys/shopsys/pull/736/files)
    ```xml
        <target name="clean-redis" description="Cleans up redis cache">
            <exec executable="${path.php.executable}" passthru="true" checkreturn="true" output="${dev.null}">
                <arg value="${path.bin-console}" />
                <arg value="shopsys:redis:clean-cache" />
            </exec>
        </target>
    ```
- *(low priority)* add a new [script](https://github.com/shopsys/project-base/blob/v7.0.0-beta6/scripts/install.sh) to `scripts/install.sh` ([#759](https://github.com/shopsys/shopsys/pull/759))
    - this script serves as a fast way to install demo instance of Shopsys Framework.
    - also this script can be used if you change the configuration of docker or app, script will apply all the changes done in these files and rebuild images.
- add a way to check if Redis is running ([#815](https://github.com/shopsys/shopsys/pull/815))
    - change version of snc/redis-bundle to ^2.1.8 in your composer.json and update dependencies with `composer update`
    - upgrade redis extension version in your `DockerFile` to version `4.1.1`
    ```diff
    -       RUN pecl install redis-4.0.2 && \
    +       RUN pecl install redis-4.1.1 && \
    ```
    - add a new phing target `redis-check` to your `build-dev.xml` and use it before any call to Redis like `clear-redis`
        - You can find inspiration in [#815](https://github.com/shopsys/shopsys/pull/815/files)
        ```xml
          <target name="redis-check" description="Checks availability of Redis">
              <exec executable="${path.php.executable}" passthru="true" checkreturn="true">
                  <arg value="${path.bin-console}" />
                  <arg value="shopsys:redis:check-availability" />
              </exec>
          </target>
        ```
- update PHPStan to the 0.11 version ([#826](https://github.com/shopsys/shopsys/pull/826))
    - change version of phpstan/phpstan to ^0.11 in your composer.json and update dependencies with `composer update`
    - add `- '#Undefined variable: \$undefined#'` as ignored error to `phpstan.neon` configuration file
    - you may need to change `StdClass` to `stdClass` in `tests/ShopBundle/Functional/Component/Grid/Ordering/GridOrderingFacadeTest.php` to pass PHPStan check

### Database migrations
- after running database migrations, all your countries across domains will be merged together and original names will be added as translations ([#762](https://github.com/shopsys/shopsys/pull/762))
    - all your countries have to have country code filled in
    - country not present on some domain will use country code as its name and will be disabled on that domain
    - please check [`Version20190121094400`](https://github.com/shopsys/shopsys/blob/v7.0.0-beta6/packages/framework/src/Migrations/Version20190121094400.php)

### Application
- use configuration file to define directories that needs to be created during build of the application ([#781](https://github.com/shopsys/shopsys/pull/781))
    - create new configuration file `app/config/directories.yml` with 2 types of directories `public_directories` and `internal_directories` and add this file into `$configs` array in `AppKernel::getConfigs()`.
    - if you had implemented your individual directories in `CreateApplicationDirectoriesCommand`, delete your extension of a class and add the directories into `app/config/directories.yml` and fill them into sections, you can read more about sections [here](https://github.com/shopsys/shopsys/blob/v7.0.0-beta6/docs/intruduction/directories.yml)
- if you were using `oneup/flysystembundle` for using different adapter than the local one, you must now implement `FilesystemFactoryInterface` and init the adapter by yourself ([#730](https://github.com/shopsys/shopsys/pull/730))
- *(low priority)* delete dependency on `oneup/flysystembundle` from your `composer.json` ([#730](https://github.com/shopsys/shopsys/pull/730))
    - delete `app/config/packages/oneup_flysystem.yml`
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
    - *(low priority)* add an extension for `OrderItemData` and `OrderItemDataFactory` and register them in `services.yml`
    - remove non-existing test cases from `EntityExtensionTest`
        - remove `ExtendedOrder*` classes
        - remove calling `doTestExtendedEntityInstantiation` with classes that are removed
        - change `ExtendedOrderItem` to standard class - remove `abstract` and inheritance annotations
        - change `doTestExtendedOrderItemsPersistence` to test only `OrderItem`
        - please find inspiration in [pull request](https://github.com/shopsys/shopsys/pull/715/files)
- *(low priority)* allow support for multiple image sizes ([#766](https://github.com/shopsys/shopsys/pull/766))
    - implement action `getAdditionalImageAction()` in `Front/ImageController.php` (or copy it from [ImageController.php](https://github.com/shopsys/project-base/blob/v7.0.0-beta6/src/Shopsys/ShopBundle/Controller/Front/ImageController.php))
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
- `Cart` has been slightly refactored ([#765](https://github.com/shopsys/shopsys/pull/765/)), so change your usages appropriately:
    - property `cartItems` has been renamed to `items`
    - method `getCartItemById` has been renamed to `getItemById`
    - method `getQuantifiedProductsIndexedByCartItemId` has been renamed to `getQuantifiedProductsIndexedByItemId`
    - method `findSimilarCartItemByCartItem` has been renamed to `findSimilarItemByItem`
    - `Cart` is now entity, so change your usages appropriately:
        - properties `user` and `cart_identifier` were moved from `CartItem` to `Cart`
            - there are new methods `getCartIdentifier`, `setModifeiedNow` and `setModifiedAt`
        - added new property `modifiedAt`
        - method's `addProduct` first parameter `CustomerIdentifier` has been removed
        - method's `mergeWithCart` third parameter `CustomerIdentifier` has been removed
        - the implementation of methods `addItem`, `removeItemyId`, `getItems`, `getItemsCount`, `changeQuantities`, `getQuantifiedProductsIndexedByItemId`, `mergeWithCart` and `findSimilarItemByItem` has been changed so revise them if you extended them
    - `CartFactory` has been refactored, so change your usages appropriately:
        - methods `get` and `createNewCart` has been removed, use `CartFacade`'s `findCartOfCurrentCustomer`, `findCartByCustomerIdentifier`, `getCartOfCurrentCustomerCreateIfNotExists` or `getCartByCustomerIdentifierCreateIfNotExists` instead
            - methods starting with `find` can return `null`
            - methods ending with `CreateIfNotExists` will always create new `Cart` in database, so use this methods only in case you are adding some items
        - method `clearCache` has been removed
        - attributes `carts`, `cartItemRepository` and `cartWatcherFacade` has been removed
    - `CartFacade` has been refactored, so change your usages appropriately:
        - it has four new methods for working with `Cart`
            - methods `findCartOfCurrentCustomer` and `findCartByCustomerIdentifier` will return `Cart` if it contains at least one `CartItem` or `null` if it is empty
            - methods `getCartOfCurrentCustomerCreateIfNotExists` and `getCartByCustomerIdentifierIfNotExists` will return `Cart` if it contains at least one `CartItem` or create one if it does not
            - instead `getCartOfCurrentCustomer` use `getCartOfCurrentCustomerCreateIfNotExists`
        - set default locale for test environment, add a `app/config/packages/test/prezent_doctrine_translatable.yml` with content
            ```
            prezent_doctrine_translatable:
                fallback_locale: en
            ```
        - the implementation of methods `addProductToCart`, `changeQuantities`, `deleteCartItem`, `getQuantifiedProductsOfCurrentCustomerIndexedByCartItemId` and `deleteOldCarts` has been changed so revise them if you extended them
        - method `cleanCart` has been removed, use method `deleteCart` instead
    - `CartMigrationFacade` has been refactored, so change your usages appropriately:
        - the constructor has changed
        - implementation of methods `mergeCurrentCartWithCart` and `onKernelController` has been changed, so revise them if you extended them
    - `CartItemRepository` class has been removed and all its logic has been moved to new `CartRepository`
    - `CartItemFactory::create` and `CartItemFactoryInterface::create` methods has changed, its first parameter is no longer `CustomerIdentifier` but `Cart`
    - In twig templates (e.g. `Front/Content/Cart/index.html.twig` and `Front/Inline/Cart/cartBox.html.twig`), use `cart is not null` instead of `cart.itemsCount > 0` and `cart.isEmpty`
    - You would probably need to modify your tests as well, because of changes in `Cart`. You would need to revise the following classes:
        - `Unit/Model/Cart/CartFactoryTest.php` has been removed
        - `Unit/Model/Cart/CartTest.php` has been changed
        - `Functional/Model/Cart/CartFacadeDeleteOldCartsTest.php` has been changed
        - `Functional/Model/Cart/CartFacadeTest.php` has been changed
        - `Functional/Model/Cart/CartItemTest.php` has been changed
        - `Functional/Model/Cart/CartTest.php` has been changed
        - `Functional/Model/Cart/Watcher/CartWatcherTest.php` has been changed
        - `Functional/Model/Order/OrderFacadeTest.php` has been changed
- *(low priority)* upgrade npm packages to the latest version ([#755](https://github.com/shopsys/shopsys/pull/755))
    - remove all npm packages by removing folder `project-base/node_modules` and `project-base/package-lock.json`
    - run command `php phing npm`
    - in order to pass standards tests you also need to run `php phing eslint-fix` to let ESlint npm package update your JavaScript files. After that your syntax should be updated to latest JavaScript standards checked by ESLint.
        - there could be error about `no-self-assign` for `document.location` in `src/Shopsys/ShopBundle/Resources/scripts/frontend/promoCode.js` and it could be solved by replacing `document.location = document.location` with `document.location.reload()` ([#809](https://github.com/shopsys/shopsys/pull/809))
- unify countries across domains with translations and domain dependency ([#762](https://github.com/shopsys/shopsys/pull/762))
    - fix new entity `Country` creation (either using factory or directly) as it changed its constructor and `CountryFactory::create` method signature (removed argument `domainId`)
        - do not forget to fix `PersonalDataExportXmlTest`
    - adjust usages of `CountryFacade`
        - method `create` no longer has second argument `domainId`
        - remove usages of methods `getAllByDomainId` and `getAllOnCurrentDomain` as they were deleted
            - use new methods `getAllEnabledOnDomain` and `getAllEnabledOnCurrentDomain` (methods returns only enabled countries)
            - change usages in `BillingAddressFormType`, `DeliveryAddressFormType` and `PersonalInfoFormType` in your implementation
            - fix `CountryFacade` mock in `PersonalInfoFormTypeTest` – mock method `getAllEnabledOnDomain` instead of `getAllByDomainId`
            - if you use `Country::getName()` in your code, fix all the usages of the method as it now needs proper locale as an argument
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
    - rename `CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1` constant to `CountryDataFixture::COUNTRY_CZECH_REPUBLIC`
- if you have extended `Localization` class, you have to add type-hints to extended methods because they were added in the parent class ([#806](https://github.com/shopsys/shopsys/pull/806))
    - if you have extended method `Localization::getAdminLocale()` only to have administration in a different language than english, you can delete it and set parameter `shopsys.admin_locale` in your `parameters.yml` file instead
- fixed JS validation of forms in popup windows ([#782](https://github.com/shopsys/shopsys/pull/782))
    - login form in popup is now loaded via AJAX
    - in `window.js` add options `textHeading = ''` and `cssClassHeading: ''` to `var defaults` like this:
        ```diff
            var defaults = {
                content: '',
                buttonClose: true,
                buttonCancel: false,
                buttonContinue: false,
                textContinue: Shopsys.translator.trans('Yes'),
                textCancel: Shopsys.translator.trans('No'),
        +       textHeading: '',
                urlContinue: '#',
                cssClass: 'window-popup--standard',
                cssClassContinue: '',
                cssClassCancel: '',
        +       cssClassHeading: '',
                closeOnBgClick: true,
                eventClose: function () {},
                eventContinue: function () {},
                eventCancel: function () {},
                eventOnLoad: function () {}
            };
        ```
        - then add the heading to `$windowContent` and add div with `js-validation-errors` class to every popup.:
        ```diff
        -   var $windowContent = $('<div class="js-window-content window-popup__in"></div>').html(options.content);
        +   var $windowContent = $('<div class="js-window-content window-popup__in"></div>');
        +   if (options.textHeading !== '') {
        +       $windowContent.append('<h2 class="' + options.cssClassHeading + '">' + options.textHeading + '</h2>');
        +   }
        +   $windowContent.append(
        +       '<div class="display-none in-message in-message--alert js-window-validation-errors"></div>'
        +       + options.content
        +   );
        ```
    - *(low priority)* change login form so it is loaded by AJAX and works with JS validation and change `Login/windowForm.html.twig`, `Login/loginForm.html.twig` and `header.html.twig` templates
        - you can change it as it was done in this [commit](https://github.com/shopsys/shopsys/pull/782/commits/ba1c946c02c14a0155c6625a63e198e8893833e9)
- if you have extended classes from `Shopsys\FrameworkBundle\Model`, `Shopsys\FrameworkBundle\Component` or `Shopsys\FrameworkBundle\DataFixtures\Demo` namespace ([#788](https://github.com/shopsys/shopsys/pull/788))
    - you need to adjust extended methods and fields to `protected` visibility because all `private` visibilities from these namespaces were changed to `protected`
    - you can delete methods that you just copied due to inability to inherit
- microservices has been removed and their functionality has been moved to framework ([#793](https://github.com/shopsys/shopsys/pull/793/)):
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
- if you have extended any of following factories, provide `Domain` object to parent constructor ([#787](https://github.com/shopsys/shopsys/pull/787))
    - `AvailabilityDataFactory`
    - `FlagDataFactory`
    - `OrderStatusDataFactory`
    - `ParameterDataFactory`
    - `UnitDataFactory`
- (*low priority*) if you want to test the flow of promo code in cart page, implement new acceptance tests ([#825](https://github.com/shopsys/shopsys/pull/825))
    - `Tests\ShopBundle\Acceptance\acceptance\CartCest`
    - `Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\CartPage`
    - `src/Shopsys/ShopBundle/Resources/views/Front/Content/Order/PromoCode/index.html.twig`

## [shopsys/product-feed-heureka]
- if you have extended class HeurekaCategoryDownloader or HeurekaCategoryCronModule ([#788](https://github.com/shopsys/shopsys/pull/788))
    - you need to adjust already extended methods and fields to `protected` visibility because all `private` visibilities from these namespaces were changed to `protected`
    - you can delete methods that you just copied due to inability to inherit

[Upgrade from v7.0.0-beta5 to v7.0.0-beta6]: https://github.com/shopsys/shopsys/compare/v7.0.0-beta5...v7.0.0-beta6
[shopsys/framework]: https://github.com/shopsys/framework
[shopsys/product-feed-heureka]: https://github.com/shopsys/product-feed-heureka
