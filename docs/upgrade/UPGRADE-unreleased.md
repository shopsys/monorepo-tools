# [Upgrade from v7.0.0-beta5 to Unreleased]

This guide contains instructions to upgrade from version v7.0.0-beta5 to Unreleased.

**Before you start, don't forget to take a look at [general instructions](/UPGRADE.md) about upgrading.**
There you can find links to upgrade notes for other versions too.

## [shopsys/framework]
### Infrastructure
- *(optional)* in your `docker/php-fpm/Dockerfile` change base image to `php:7.3-fpm-stretch` ([#694](https://github.com/shopsys/shopsys/pull/694))

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
[shopsys/microservice-product-search]: https://github.com/shopsys/microservice-product-search
[shopsys/microservice-product-search-export]: https://github.com/shopsys/microservice-product-search-export
